<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    /**
     * Get all movies with pagination and filtering
     */
    public function index(Request $request)
    {
        $query = Movie::query();

        // Search by title
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }

        // Filter by genre
        if ($request->has('genre')) {
            $query->where('genre', $request->input('genre'));
        }

        // Filter by year
        if ($request->has('year')) {
            $query->where('release_year', $request->input('year'));
        }

        // Sort options
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->input('per_page', 20);
        $movies = $query->paginate($perPage);

        return response()->json($movies);
    }

    /**
     * Get a single movie by ID
     */
    public function show($id)
    {
        $movie = Movie::findOrFail($id);
        return response()->json($movie);
    }

    /**
     * Create a new movie entry
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'required|string',
            'thumbnail_path' => 'nullable|string',
            'duration' => 'nullable|integer',
            'genre' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'rating' => 'nullable|numeric|min:0|max:10',
            'file_size' => 'nullable|integer',
            'video_codec' => 'nullable|string',
            'resolution' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $movie = Movie::create($request->all());

        return response()->json($movie, 201);
    }

    /**
     * Update a movie
     */
    public function update(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'file_path' => 'sometimes|required|string',
            'thumbnail_path' => 'nullable|string',
            'duration' => 'nullable|integer',
            'genre' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'rating' => 'nullable|numeric|min:0|max:10',
            'file_size' => 'nullable|integer',
            'video_codec' => 'nullable|string',
            'resolution' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $movie->update($request->all());

        return response()->json($movie);
    }

    /**
     * Delete a movie
     */
    public function destroy($id)
    {
        $movie = Movie::findOrFail($id);
        $movie->delete();

        return response()->json([
            'message' => 'Movie deleted successfully'
        ]);
    }

    /**
     * Get unique genres
     */
    public function genres()
    {
        $genres = Movie::whereNotNull('genre')
            ->distinct()
            ->pluck('genre')
            ->sort()
            ->values();

        return response()->json($genres);
    }

    /**
     * Get unique years
     */
    public function years()
    {
        $years = Movie::whereNotNull('release_year')
            ->distinct()
            ->pluck('release_year')
            ->sort()
            ->values();

        return response()->json($years);
    }
}
