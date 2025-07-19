<?php

namespace Marshmallow\Tiptap\Controllers;

use Marshmallow\Tiptap\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FilesController
{
    /**
     * Allowed file extensions for uploads
     */
    private function getAllowedExtensions(): array
    {
        return config('nova-tiptap.upload.allowed_file_extensions', [
            'pdf',
            'doc',
            'docx',
            'xls',
            'xlsx',
            'ppt',
            'pptx',
            'txt',
            'rtf',
            'csv',
            'zip',
            'rar',
            '7z',
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'svg',
            'mp3',
            'wav',
            'mp4',
            'avi',
            'mov',
            'wmv'
        ]);
    }

    /**
     * Maximum file size in bytes
     */
    private function getMaxFileSize(): int
    {
        return config('nova-tiptap.upload.max_file_size', 10485760); // 10MB
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
                'file',
                'max:' . ($this->getMaxFileSize() / 1024), // Laravel expects KB
                function ($attribute, $value, $fail) {
                    if (!$this->isAllowedFileType($value)) {
                        $fail('The uploaded file type is not allowed.');
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
     * Check if the uploaded file type is allowed
     */
    private function isAllowedFileType($file): bool
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
                'file' => 'File size exceeds the maximum allowed size.'
            ]);
        }

        // Check for potentially dangerous file extensions
        $dangerousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'pht', 'phar', 'exe', 'bat', 'cmd', 'sh', 'js', 'html', 'htm'];
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, $dangerousExtensions)) {
            throw ValidationException::withMessages([
                'file' => 'File type is not allowed for security reasons.'
            ]);
        }

        // Check MIME type matches extension
        $mimeType = $file->getMimeType();
        if (!$this->isValidMimeType($mimeType, $extension)) {
            throw ValidationException::withMessages([
                'file' => 'File content does not match its extension.'
            ]);
        }
    }

    /**
     * Validate MIME type against extension
     */
    private function isValidMimeType(string $mimeType, string $extension): bool
    {
        $allowedMimeTypes = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'xls' => ['application/vnd.ms-excel'],
            'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            'txt' => ['text/plain'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'gif' => ['image/gif'],
            'zip' => ['application/zip'],
        ];

        return isset($allowedMimeTypes[$extension]) &&
            in_array($mimeType, $allowedMimeTypes[$extension]);
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
        return config('nova-tiptap.file_storage.disk') ?: (config('filesystems.disks.public') ? 'public' : 'local');
    }

    /**
     * Sanitize the upload path
     */
    private function sanitizePath(?string $path): string
    {
        if (!$path) {
            return config('nova-tiptap.file_storage.path', '');
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
