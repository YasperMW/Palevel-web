import logging
from fastapi import APIRouter, Depends, HTTPException, status, Query
from sqlalchemy.orm import Session, joinedload
from sqlalchemy import desc, func
from database import get_db
from models import (
    DataDeletionRequest, DataDeletionRequestCreate, 
    DataDeletionRequestResponse, DataDeletionRequestUpdate, User
)
from endpoints.users import get_current_user
from datetime import datetime, timedelta
from typing import Optional, List
import uuid

# Logging for data deletion endpoints
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

router = APIRouter(tags=["data-deletion"])

def require_admin_user(current_user: User = Depends(get_current_user)) -> User:
    """Require admin user role"""
    if current_user.user_type != 'admin':
        raise HTTPException(
            status_code=status.HTTP_403_FORBIDDEN,
            detail="Admin access required"
        )
    return current_user


@router.post("/request", response_model=DataDeletionRequestResponse)
async def create_data_deletion_request(
    request_data: DataDeletionRequestCreate,
    db: Session = Depends(get_db)
):
    """Create a new data deletion request"""
    try:
        # Create the deletion request
        deletion_request = DataDeletionRequest(
            name=request_data.name,
            email=request_data.email,
            phone=request_data.phone,
            reason=request_data.reason,
            data_categories=request_data.data_categories,
            confirmation=request_data.confirmation,
            status="pending"
        )
        
        db.add(deletion_request)
        db.commit()
        db.refresh(deletion_request)
        
        logger.info(f"Data deletion request created: {deletion_request.request_id} for email: {request_data.email}")
        
        return deletion_request
        
    except Exception as e:
        logger.error(f"Error creating data deletion request: {str(e)}")
        db.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to create data deletion request"
        )


@router.get("/requests", response_model=List[DataDeletionRequestResponse])
async def get_data_deletion_requests(
    current_user: User = Depends(require_admin_user),
    db: Session = Depends(get_db),
    status_filter: Optional[str] = Query(None, description="Filter by status"),
    page: int = Query(1, ge=1, description="Page number"),
    limit: int = Query(50, ge=1, le=100, description="Items per page")
):
    """Get all data deletion requests (admin only)"""
    try:
        query = db.query(DataDeletionRequest).options(
            joinedload(DataDeletionRequest.admin)
        )
        
        # Apply status filter if provided
        if status_filter:
            query = query.filter(DataDeletionRequest.status == status_filter)
        
        # Apply pagination and ordering
        offset = (page - 1) * limit
        requests = query.order_by(desc(DataDeletionRequest.created_at)).offset(offset).limit(limit).all()
        
        return requests
        
    except Exception as e:
        logger.error(f"Error fetching data deletion requests: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to fetch data deletion requests"
        )


@router.get("/requests/{request_id}", response_model=DataDeletionRequestResponse)
async def get_data_deletion_request(
    request_id: uuid.UUID,
    current_user: User = Depends(require_admin_user),
    db: Session = Depends(get_db)
):
    """Get a specific data deletion request (admin only)"""
    try:
        deletion_request = db.query(DataDeletionRequest).options(
            joinedload(DataDeletionRequest.admin)
        ).filter(DataDeletionRequest.request_id == request_id).first()
        
        if not deletion_request:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Data deletion request not found"
            )
        
        return deletion_request
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error fetching data deletion request {request_id}: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to fetch data deletion request"
        )


@router.put("/requests/{request_id}", response_model=DataDeletionRequestResponse)
async def update_data_deletion_request(
    request_id: uuid.UUID,
    update_data: DataDeletionRequestUpdate,
    current_user: User = Depends(require_admin_user),
    db: Session = Depends(get_db)
):
    """Update a data deletion request status (admin only)"""
    try:
        deletion_request = db.query(DataDeletionRequest).filter(
            DataDeletionRequest.request_id == request_id
        ).first()
        
        if not deletion_request:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Data deletion request not found"
            )
        
        # Validate status transition
        valid_statuses = ["pending", "processing", "completed", "rejected"]
        if update_data.status not in valid_statuses:
            raise HTTPException(
                status_code=status.HTTP_400_BAD_REQUEST,
                detail=f"Invalid status. Must be one of: {', '.join(valid_statuses)}"
            )
        
        # Update the request
        deletion_request.status = update_data.status
        deletion_request.admin_notes = update_data.admin_notes
        deletion_request.processed_by = current_user.user_id
        deletion_request.processed_at = datetime.utcnow()
        
        db.commit()
        db.refresh(deletion_request)
        
        logger.info(f"Data deletion request {request_id} updated to status: {update_data.status} by admin: {current_user.user_id}")
        
        # Load admin relationship for response
        db.refresh(deletion_request)
        deletion_request = db.query(DataDeletionRequest).options(
            joinedload(DataDeletionRequest.admin)
        ).filter(DataDeletionRequest.request_id == request_id).first()
        
        return deletion_request
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error updating data deletion request {request_id}: {str(e)}")
        db.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to update data deletion request"
        )


@router.delete("/requests/{request_id}")
async def delete_data_deletion_request(
    request_id: uuid.UUID,
    current_user: User = Depends(require_admin_user),
    db: Session = Depends(get_db)
):
    """Delete a data deletion request (admin only)"""
    try:
        deletion_request = db.query(DataDeletionRequest).filter(
            DataDeletionRequest.request_id == request_id
        ).first()
        
        if not deletion_request:
            raise HTTPException(
                status_code=status.HTTP_404_NOT_FOUND,
                detail="Data deletion request not found"
            )
        
        db.delete(deletion_request)
        db.commit()
        
        logger.info(f"Data deletion request {request_id} deleted by admin: {current_user.user_id}")
        
        return {"message": "Data deletion request deleted successfully"}
        
    except HTTPException:
        raise
    except Exception as e:
        logger.error(f"Error deleting data deletion request {request_id}: {str(e)}")
        db.rollback()
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to delete data deletion request"
        )


@router.get("/stats")
async def get_data_deletion_stats(
    current_user: User = Depends(require_admin_user),
    db: Session = Depends(get_db)
):
    """Get data deletion request statistics (admin only)"""
    try:
        total_requests = db.query(DataDeletionRequest).count()
        pending_requests = db.query(DataDeletionRequest).filter(DataDeletionRequest.status == "pending").count()
        processing_requests = db.query(DataDeletionRequest).filter(DataDeletionRequest.status == "processing").count()
        completed_requests = db.query(DataDeletionRequest).filter(DataDeletionRequest.status == "completed").count()
        rejected_requests = db.query(DataDeletionRequest).filter(DataDeletionRequest.status == "rejected").count()
        
        # Recent requests (last 30 days)
        thirty_days_ago = datetime.utcnow() - timedelta(days=30)
        recent_requests = db.query(DataDeletionRequest).filter(
            DataDeletionRequest.created_at >= thirty_days_ago
        ).count()
        
        return {
            "total_requests": total_requests,
            "pending_requests": pending_requests,
            "processing_requests": processing_requests,
            "completed_requests": completed_requests,
            "rejected_requests": rejected_requests,
            "recent_requests": recent_requests
        }
        
    except Exception as e:
        logger.error(f"Error fetching data deletion stats: {str(e)}")
        raise HTTPException(
            status_code=status.HTTP_500_INTERNAL_SERVER_ERROR,
            detail="Failed to fetch data deletion statistics"
        )
