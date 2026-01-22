<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Config;

class MediaHelper
{
    /**
     * Resolve the full URL for a media item.
     *
     * @param string|null $path
     * @return string|null
     */
    public static function getMediaUrl(?string $path)
    {
        if (empty($path)) {
            return null;
        }

        // If it's already a full URL, return it
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        // Get the API base URL
        $baseUrl = Config::get('palevel.api_url');
        
        if (empty($baseUrl)) {
            return $path;
        }

        // Ensure base URL doesn't have trailing slash
        $baseUrl = rtrim($baseUrl, '/');
        
        // Clean the path
        $cleanPath = ltrim($path, '/');
        
        // If the path doesn't start with 'media/' and the base URL doesn't end with '/media',
        // and assuming the backend serves user uploads from /media/
        if (!str_starts_with($cleanPath, 'media/') && !str_ends_with($baseUrl, '/media')) {
            $cleanPath = 'media/' . $cleanPath;
        }
        
        // Ensure path starts with slash
        $path = '/' . $cleanPath;

        return $baseUrl . $path;
    }
}
