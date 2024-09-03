<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'creator_id',
    ];
    
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user','group_id');
    }
}
