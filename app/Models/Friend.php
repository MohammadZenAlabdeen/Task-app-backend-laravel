<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'friend_id'
    ];
    // Define the relationship to the user who initiated the friendship
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Define the relationship to the user who is the friend
    public function friend()
    {
        return $this->belongsTo(User::class, 'friend_id');
    }
}
