<?php

namespace Marshmallow\Tiptap\Controllers;

use Marshmallow\Tiptap\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ImagesController
{
    /**
     * Allowed image extensions
     */
    private function getAllowedExtensions(): array
    {
        return config('nova-tiptap.upload.allowed_image_extensions', ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']);
    }

    /**
     * Maximum file size in bytes
     */
    private function getMaxFileSize(): int
    {
        return config('nova-tiptap.upload.max_image_size', 5242880); // 5MB
    }

    /**
     * Allowed storage disks
     */
    private function getAllowedDisks(): array
    {
        return config('nova-tiptap.upload.allowed_disks', ['public', 'local']);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                'image',
                'max:' . ($this->getMaxFileSize() / 1024), // Laravel expects KB
                function ($attribute, $value, $fail) {
                    if (!$this->isAllowedImageType($value)) {
                        $fail('The uploaded image type is not allowed.');
                    }
                }
            ],
            'disk' => 'nullable|string|in:' . implode(',', $this->getAllowedDisks()),
            'path' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $file = $request->file('file');

        // Additional security checks
        $this->performSecurityChecks($file);

        // Determine disk with security constraints
        $disk = $this->getDisk($request->input('disk'));

        // Sanitize path
        $path = $this->sanitizePath($request->input('path', ''));

        // Generate a secure filename
        $fileName = $this->generateSecureFilename($file);

        // Ensure unique filename
        if (Storage::disk($disk)->exists($path . '/' . $fileName)) {
            $fileService = new FileService();
            $fileName = $fileService->uniqifyName($fileName);
        }

        // Store the file
        $newPath = $file->storeAs($path, $fileName, $disk);

        $url = Storage::disk($disk)->url($newPath);

        return response()->json([
            'url' => $url,
        ]);
    }

    /**
     * Check if the uploaded image type is allowed
     */
    private function isAllowedImageType($file): bool
    {
        $extension = strtolower($file->getClientOriginalExtension());
        return in_array($extension, $this->getAllowedExtensions());
    }

    /**
     * Perform additional security checks on the uploaded file
     */
    private function performSecurityChecks($file): void
    {
        // Check file size
        if ($file->getSize() > $this->getMaxFileSize()) {
            throw ValidationException::withMessages([
                'file' => 'Image size exceeds the maximum allowed size.'
            ]);
        }

        // Verify it's actually an image using getimagesize
        $imageInfo = @getimagesize($file->getPathname());
        if ($imageInfo === false) {
            throw ValidationException::withMessages([
                'file' => 'File is not a valid image.'
            ]);
        }

        // Check for potentially dangerous file extensions
        $extension = strtolower($file->getClientOriginalExtension());

        // For SVG files, perform additional security checks
        if ($extension === 'svg') {
            $this->validateSvgFile($file);
        }
    }

    /**
     * Validate SVG file for security issues
     */
    private function validateSvgFile($file): void
    {
        $content = file_get_contents($file->getPathname());

        // Check for potentially dangerous SVG content
        $dangerousPatterns = [
            '/<script/i',
            '/javascript:/i',
            '/onclick/i',
            '/onload/i',
            '/onerror/i',
            '/<iframe/i',
            '/<object/i',
            '/<embed/i',
            '/xlink:href="data:/i'
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $content)) {
                throw ValidationException::withMessages([
                    'file' => 'SVG file contains potentially dangerous content.'
                ]);
            }
        }
    }

    /**
     * Get disk with security constraints
     */
    private function getDisk(?string $requestedDisk): string
    {
        // Only allow specific disks for security
        if ($requestedDisk && in_array($requestedDisk, $this->getAllowedDisks())) {
            return $requestedDisk;
        }

        // Default to configured disk or public
        return config('nova-tiptap.image_storage.disk') ?: (config('filesystems.disks.public') ? 'public' : 'local');
    }

    /**
     * Sanitize the upload path
     */
    private function sanitizePath(?string $path): string
    {
        if (!$path) {
            return config('nova-tiptap.image_storage.path', '');
        }

        // Remove dangerous characters and path traversal attempts
        $path = preg_replace('/[^a-zA-Z0-9\/_-]/', '', $path);
        $path = str_replace(['../', './'], '', $path);

        // Trim slashes
        $path = trim($path, '/');

        return $path;
    }

    /**
     * Generate a secure filename
     */
    private function generateSecureFilename($file): string
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Remove potentially dangerous characters from filename
        $baseName = pathinfo($originalName, PATHINFO_FILENAME);
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $baseName);

        // Ensure filename is not too long
        $safeName = substr($safeName, 0, 100);

        return $safeName . '.' . $extension;
    }
}
