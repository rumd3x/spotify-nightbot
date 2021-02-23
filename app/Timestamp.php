<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Timestamp extends Model
{
    use SoftDeletes;

    const ENTRY_STATE_EXIT = false;
    const ENTRY_STATE_ENTER = true;

    protected $fillable = [
        'user_id',
        'date',
        'time',
        'entry',
    ];

    protected $casts = [
        'entry' => 'boolean',
    ];

    public function user()
    {
        $this->belongsTo(User::class);
    }

    public function getFormattedEntryAttribute()
    {
        return $this->entry ? 'entry' : 'exit';
    }

    public function getCarbonAttribute()
    {
        return Carbon::parse(sprintf("%s %s", $this->date, $this->time));
    }
}
