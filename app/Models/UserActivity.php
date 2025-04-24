<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    //
    function user()
    {
        return $this->belongsTo(User::class);
    }
    protected $fillable = [
        'user_id',
        'activity_type',
        'points',
        'rank',
    ];
    protected $casts = [
        'user_id' => 'integer',
        'points' => 'integer',
    ];
}
