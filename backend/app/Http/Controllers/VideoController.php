<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoController extends Controller
{
    /**
     * Stream video with range request support
     * This enables seeking in the video player
     */
    public function stream(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);
        $path = $movie->file_path;

        // Check if file exists
        if (!file_exists($path)) {
            return response()->json([
                'error' => 'Video file not found on server'
            ], 404);
        }

        $fileSize = filesize($path);
        $mimeType = $this->getMimeType($path);

        // Get range header
        $range = $request->header('Range');

        if ($range) {
            // Parse range header
            list($start, $end) = $this->parseRange($range, $fileSize);

            if ($start > $end || $start > $fileSize - 1 || $end > $fileSize - 1) {
                return response('', 416, [
                    'Content-Range' => "bytes */{$fileSize}",
                ]);
            }

            $length = $end - $start + 1;

            $headers = [
                'Content-Type' => $mimeType,
                'Content-Length' => $length,
                'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'no-cache, must-revalidate',
            ];

            return response()->stream(function () use ($path, $start, $length) {
                $stream = fopen($path, 'rb');
                fseek($stream, $start);
                
                $chunkSize = 1024 * 256; // 256KB chunks
                $bytesRead = 0;

                while (!feof($stream) && $bytesRead < $length) {
                    $bytesToRead = min($chunkSize, $length - $bytesRead);
                    echo fread($stream, $bytesToRead);
                    $bytesRead += $bytesToRead;
                    flush();
                }

                fclose($stream);
            }, 206, $headers);
        }

        // No range header - stream entire file
        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-cache, must-revalidate',
        ];

        return response()->stream(function () use ($path) {
            $stream = fopen($path, 'rb');
            fpassthru($stream);
            fclose($stream);
        }, 200, $headers);
    }

    /**
     * Parse range header
     */
    private function parseRange($range, $fileSize)
    {
        $range = str_replace('bytes=', '', $range);
        $parts = explode('-', $range);

        $start = intval($parts[0]);
        $end = isset($parts[1]) && $parts[1] !== '' ? intval($parts[1]) : $fileSize - 1;

        return [$start, $end];
    }

    /**
     * Get MIME type based on file extension
     */
    private function getMimeType($path)
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $mimeTypes = [
            'mp4' => 'video/mp4',
            'mkv' => 'video/x-matroska',
            'avi' => 'video/x-msvideo',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'webm' => 'video/webm',
        ];

        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}
