<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaybackSummary extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'artist',
        'song',
        'playback_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
