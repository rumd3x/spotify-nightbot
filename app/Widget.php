<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Widget extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'code',
        'font_family',
        'font_size',
        'text_color',
        'background_color',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
