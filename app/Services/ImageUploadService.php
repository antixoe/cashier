<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageUploadService
{
    /**
     * Upload an image file
     * @param UploadedFile $file
     * @param string $directory The storage directory (e.g., 'products', 'users')
     * @return string|null The file path or null if upload fails
     */
    public static function upload(UploadedFile $file, string $directory = 'images'): ?string
    {
        try {
            // Validate file
            if (!in_array($file->getClientMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                throw new \Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
            }

            if ($file->getSize() > 5 * 1024 * 1024) { // 5MB max
                throw new \Exception('File size exceeds 5MB limit.');
            }

            // Generate unique filename
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            
            // Store the file
            $path = $file->storeAs("public/{$directory}", $filename);
            
            // Return the path without the 'public/' prefix for asset access
            return str_replace('public/', '', $path);
        } catch (\Exception $e) {
            \Log::error('Image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete an image file
     * @param string $path The file path
     */
    public static function delete(?string $path): void
    {
        if (!empty($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Get the full URL for an image
     * @param string|null $path
     * @param string $default Default image URL if path is null
     * @return string
     */
    public static function getUrl(?string $path, string $default = '/images/placeholder.png'): string
    {
        if (empty($path)) {
            return $default;
        }
        
        return asset("storage/{$path}");
    }
}
