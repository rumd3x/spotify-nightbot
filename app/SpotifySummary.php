<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SpotifySummary extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'spotify_user_id',
        'artist',
        'song',
        'playback_status',
    ];

    public function spotify()
    {
        return $this->belongsTo(SpotifyUser::class);
    }
}
