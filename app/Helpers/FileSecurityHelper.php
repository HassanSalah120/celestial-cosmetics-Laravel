<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FileSecurityHelper
{
    /**
     * Safe MIME types for images
     */
    protected static $safeImageTypes = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
    ];
    
    /**
     * Safe MIME types for documents
     */
    protected static $safeDocumentTypes = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        'text/plain',
        'text/csv',
    ];
    
    /**
     * Validate uploaded file security
     *
     * @param UploadedFile $file
     * @param array $allowedTypes
     * @param int $maxSize In KB
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateFile(UploadedFile $file, array $allowedTypes = [], int $maxSize = 10240)
    {
        // Default to image types if no types specified
        if (empty($allowedTypes)) {
            $allowedTypes = self::$safeImageTypes;
        }
        
        // Check if file is valid
        if (!$file->isValid()) {
            return [
                'valid' => false,
                'message' => 'The uploaded file is not valid'
            ];
        }
        
        // Check file size
        if ($file->getSize() > $maxSize * 1024) {
            return [
                'valid' => false,
                'message' => "File size exceeds maximum allowed size ({$maxSize}KB)"
            ];
        }
        
        // Verify MIME type with PHP's finfo as an extra layer of security
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file->getPathname());
        
        if (!in_array($mime, $allowedTypes)) {
            Log::channel('security')->warning('Invalid file type upload attempt', [
                'uploaded_mime' => $mime,
                'client_mime' => $file->getClientMimeType(),
                'extension' => $file->getClientOriginalExtension(),
                'user_id' => auth()->id() ?? 'unauthenticated',
            ]);
            
            return [
                'valid' => false,
                'message' => 'The file type is not allowed'
            ];
        }
        
        // For SVG files, perform additional security checks
        if ($mime === 'image/svg+xml') {
            $svgValidation = self::validateSvgContent(file_get_contents($file->getPathname()));
            if (!$svgValidation['valid']) {
                return $svgValidation;
            }
        }
        
        return [
            'valid' => true,
            'message' => 'File is valid'
        ];
    }
    
    /**
     * Validate SVG file content for security issues
     *
     * @param string $content
     * @return array ['valid' => bool, 'message' => string]
     */
    public static function validateSvgContent(string $content)
    {
        // Check for potentially dangerous elements
        $dangerousTags = [
            'script',
            'iframe',
            'object',
            'embed',
            'foreignObject',
            'handler',
            'onload',
            'onclick',
            'onmouseover',
            'eval',
            'javascript:'
        ];
        
        foreach ($dangerousTags as $tag) {
            if (stripos($content, $tag) !== false) {
                Log::channel('security')->warning('Potentially malicious SVG upload detected', [
                    'dangerous_element' => $tag,
                    'user_id' => auth()->id() ?? 'unauthenticated'
                ]);
                
                return [
                    'valid' => false,
                    'message' => 'SVG contains potentially harmful content'
                ];
            }
        }
        
        return [
            'valid' => true,
            'message' => 'SVG content is valid'
        ];
    }
    
    /**
     * Generate a secure, random filename
     *
     * @param UploadedFile $file
     * @param string $prefix
     * @return string
     */
    public static function generateSecureFilename(UploadedFile $file, string $prefix = '')
    {
        $extension = $file->getClientOriginalExtension();
        $safeExtension = self::sanitizeExtension($extension);
        
        // Generate a UUID-based filename
        return $prefix . Str::uuid()->toString() . '.' . $safeExtension;
    }
    
    /**
     * Sanitize file extension
     *
     * @param string $extension
     * @return string
     */
    public static function sanitizeExtension(string $extension)
    {
        // Remove any characters that aren't alphanumeric
        return preg_replace('/[^a-zA-Z0-9]/', '', $extension);
    }
    
    /**
     * Get allowed file types for a specific context
     *
     * @param string $context 'image', 'document', 'all'
     * @return array
     */
    public static function getAllowedTypes(string $context = 'image')
    {
        switch ($context) {
            case 'image':
                return self::$safeImageTypes;
            case 'document':
                return self::$safeDocumentTypes;
            case 'all':
                return array_merge(self::$safeImageTypes, self::$safeDocumentTypes);
            default:
                return self::$safeImageTypes;
        }
    }
} 