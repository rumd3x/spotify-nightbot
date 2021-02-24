<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Integration extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'spotify_refresh_token',
        'nightbot_refresh_token',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
