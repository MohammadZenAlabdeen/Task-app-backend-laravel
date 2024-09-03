<?php

namespace App\Models;

use Illuminate\Console\View\Components\Task as ComponentsTask;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'dead_line',
        'user_id',
        'group_id',
    ];

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class,'task_user','task_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
