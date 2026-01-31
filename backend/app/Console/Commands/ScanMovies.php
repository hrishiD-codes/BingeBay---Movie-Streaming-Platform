<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use getID3;

class ScanMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movies:scan {path? : The directory path to scan}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan a directory for movie files and add them to the database';

    /**
     * Supported video extensions
     */
    private $videoExtensions = ['mp4', 'mkv', 'avi', 'mov', 'wmv', 'flv', 'webm', 'm4v'];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = $this->argument('path') ?: 'd:\Personal_projects\streamerz\media';

        if (!is_dir($path)) {
            $this->error("Directory not found: {$path}");
            return 1;
        }

        $this->info("Scanning directory: {$path}");
        $this->info("Looking for video files...\n");

        $files = $this->getVideoFiles($path);
        $totalFiles = count($files);

        if ($totalFiles === 0) {
            $this->warn("No video files found in the specified directory.");
            return 0;
        }

        $this->info("Found {$totalFiles} video file(s)\n");

        $bar = $this->output->createProgressBar($totalFiles);
        $bar->start();

        $added = 0;
        $skipped = 0;

        foreach ($files as $file) {
            $result = $this->processFile($file);
            
            if ($result) {
                $added++;
            } else {
                $skipped++;
            }

            $bar->advance();
        }

        $bar->finish();

        $this->newLine(2);
        $this->info("Scan complete!");
        $this->info("Added: {$added}");
        $this->info("Skipped (already exists): {$skipped}");

        return 0;
    }

    /**
     * Get all video files from directory recursively
     */
    private function getVideoFiles($directory)
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, $this->videoExtensions)) {
                    $files[] = $file->getPathname();
                }
            }
        }

        return $files;
    }

    /**
     * Process a single video file
     */
    private function processFile($filePath)
    {
        // Check if file already exists in database
        $existingMovie = Movie::where('file_path', $filePath)->first();
        
        if ($existingMovie) {
            return false; // Skip
        }

        // Extract file information
        $fileName = pathinfo($filePath, PATHINFO_FILENAME);
        $fileSize = filesize($filePath);
        
        // Try to extract metadata
        $metadata = $this->extractMetadata($filePath);

        // Create movie entry
        Movie::create([
            'title' => $this->cleanTitle($fileName),
            'description' => null,
            'file_path' => $filePath,
            'thumbnail_path' => null,
            'duration' => $metadata['duration'] ?? null,
            'genre' => null,
            'release_year' => $this->extractYear($fileName),
            'rating' => null,
            'file_size' => $fileSize,
            'video_codec' => $metadata['codec'] ?? null,
            'resolution' => $metadata['resolution'] ?? null,
        ]);

        return true; // Added
    }

    /**
     * Extract metadata from video file
     */
    private function extractMetadata($filePath)
    {
        $metadata = [
            'duration' => null,
            'codec' => null,
            'resolution' => null,
        ];

        // Try to use getID3 if available
        if (class_exists('getID3')) {
            try {
                $getID3 = new getID3();
                $fileInfo = $getID3->analyze($filePath);

                if (isset($fileInfo['playtime_seconds'])) {
                    $metadata['duration'] = (int) $fileInfo['playtime_seconds'];
                }

                if (isset($fileInfo['video']['codec'])) {
                    $metadata['codec'] = $fileInfo['video']['codec'];
                }

                if (isset($fileInfo['video']['resolution_x']) && isset($fileInfo['video']['resolution_y'])) {
                    $metadata['resolution'] = $fileInfo['video']['resolution_x'] . 'x' . $fileInfo['video']['resolution_y'];
                }
            } catch (\Exception $e) {
                // Silently fail
            }
        }

        return $metadata;
    }

    /**
     * Clean up title from filename
     */
    private function cleanTitle($fileName)
    {
        // Remove common patterns
        $title = preg_replace('/[\._]/', ' ', $fileName);
        $title = preg_replace('/\d{4}/', '', $title); // Remove year
        $title = preg_replace('/\[.*?\]/', '', $title); // Remove brackets
        $title = preg_replace('/\(.*?\)/', '', $title); // Remove parentheses
        $title = preg_replace('/\s+/', ' ', $title); // Normalize spaces
        
        return trim($title);
    }

    /**
     * Extract year from filename
     */
    private function extractYear($fileName)
    {
        if (preg_match('/\b(19\d{2}|20\d{2})\b/', $fileName, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
