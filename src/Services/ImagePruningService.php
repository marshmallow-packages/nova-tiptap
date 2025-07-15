<?php

namespace Marshmallow\Tiptap\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImagePruningService
{
    /**
     * Prune images that are no longer present in the content.
     *
     * @param string|null $oldContent
     * @param string|null $newContent
     * @param array $imageSettings
     * @return array Array of pruned file paths
     */
    public static function pruneImages($oldContent, $newContent, array $imageSettings = []): array
    {
        $oldImages = self::extractUploadedImages($oldContent);
        $newImages = self::extractUploadedImages($newContent);

        $imagesToPrune = array_diff($oldImages, $newImages);

        $prunedFiles = [];

        foreach ($imagesToPrune as $imageUrl) {
            if (self::deleteImageFile($imageUrl, $imageSettings)) {
                $prunedFiles[] = $imageUrl;
            }
        }

        return $prunedFiles;
    }

    /**
     * Extract uploaded image URLs from content.
     * Only extracts images with tt-mode="file" (uploaded files, not external URLs).
     *
     * @param string|null $content
     * @return array
     */
    protected static function extractUploadedImages($content): array
    {
        if (empty($content)) {
            return [];
        }

        $images = [];

        // Pattern to match img tags with tt-mode="file" - handles attributes in any order
        $pattern = '/<img[^>]*\btt-mode\s*=\s*["\']file["\'][^>]*>/i';

        if (preg_match_all($pattern, $content, $matches)) {
            // Extract src attributes from the matched img tags
            foreach ($matches[0] as $imgTag) {
                if (preg_match('/\bsrc\s*=\s*["\']([^"\']*)["\']/', $imgTag, $srcMatch)) {
                    $src = trim($srcMatch[1]);
                    if (!empty($src)) {
                        $images[] = $src;
                    }
                }
            }
        }

        return array_unique($images);
    }

    /**
     * Delete an image file from storage.
     *
     * @param string $imageUrl
     * @param array $imageSettings
     * @return bool
     */
    protected static function deleteImageFile(string $imageUrl, array $imageSettings = []): bool
    {
        try {
            $disk = $imageSettings['disk'] ?? config('nova-tiptap.image_storage.disk', 'public');
            $storage = Storage::disk($disk);

            // Get the file path from the URL
            $filePath = self::getFilePathFromUrl($imageUrl, $disk);

            if ($filePath && $storage->exists($filePath)) {
                return $storage->delete($filePath);
            }

            return false;
        } catch (\Exception $e) {
            // Log the error but don't fail the whole operation
            Log::warning('Failed to delete TipTap image file: ' . $e->getMessage(), [
                'image_url' => $imageUrl,
                'settings' => $imageSettings
            ]);

            return false;
        }
    }

    /**
     * Extract file path from storage URL.
     *
     * @param string $url
     * @param string $disk
     * @return string|null
     */
    protected static function getFilePathFromUrl(string $url, string $disk): ?string
    {
        // Parse the URL to extract the path
        $urlParts = parse_url($url);
        if (!isset($urlParts['path'])) {
            return null;
        }

        $path = ltrim($urlParts['path'], '/');

        // Remove common storage path prefixes
        $path = preg_replace('/^storage\//', '', $path);

        // For public disk, handle common patterns
        if ($disk === 'public') {
            // Remove app URL if present
            $appUrl = config('app.url');
            if ($appUrl && Str::startsWith($url, $appUrl)) {
                $path = Str::after($url, $appUrl . '/storage/');
            }
        }

        return $path ?: null;
    }
}
