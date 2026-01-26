-- Create data_deletion_requests table
CREATE TABLE IF NOT EXISTS data_deletion_requests (
    request_id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    reason TEXT NOT NULL,
    data_categories JSONB NOT NULL,
    confirmation BOOLEAN NOT NULL DEFAULT FALSE,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    admin_notes TEXT,
    processed_by UUID REFERENCES users(user_id) ON DELETE SET NULL,
    processed_at TIMESTAMP WITH TIME ZONE,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT NOW()
);

-- Create indexes for better performance
CREATE INDEX IF NOT EXISTS idx_data_deletion_requests_email ON data_deletion_requests(email);
CREATE INDEX IF NOT EXISTS idx_data_deletion_requests_status ON data_deletion_requests(status);
CREATE INDEX IF NOT EXISTS idx_data_deletion_requests_created_at ON data_deletion_requests(created_at);
CREATE INDEX IF NOT EXISTS idx_data_deletion_requests_processed_by ON data_deletion_requests(processed_by);

-- Add constraint for valid status values
ALTER TABLE data_deletion_requests 
ADD CONSTRAINT chk_data_deletion_status 
CHECK (status IN ('pending', 'processing', 'completed', 'rejected'));

-- Add trigger to automatically update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = NOW();
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_data_deletion_requests_updated_at 
    BEFORE UPDATE ON data_deletion_requests 
    FOR EACH ROW 
    EXECUTE FUNCTION update_updated_at_column();
