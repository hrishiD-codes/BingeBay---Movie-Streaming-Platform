<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchHistory extends Model
{
    use HasFactory;

    protected $table = 'watch_history';

    protected $fillable = [
        'user_id',
        'movie_id',
        'watch_position',
        'completed',
        'last_watched_at',
    ];

    protected $casts = [
        'watch_position' => 'integer',
        'completed' => 'boolean',
        'last_watched_at' => 'datetime',
    ];

    /**
     * Get the user that owns the watch history
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the movie that this watch history belongs to
     */
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
