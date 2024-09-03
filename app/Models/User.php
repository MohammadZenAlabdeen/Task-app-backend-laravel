<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens as SanctumHasApiTokens;
use HasApiTokens; // Add this line
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use SanctumHasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];
    protected $casts = [
        'password' => 'hashed',
    ];
        public function tasksCreated(){
            return $this->hasMany(Task::class);
        }
        public function groups(){
            return $this->belongsToMany(Group::class,'group_user','user_id');
        }
        public function assignedTasks(){
            return $this->belongsToMany(Task::class,'task_user','user_id');
        }
        public function friendRequestsSent()
        {
            return $this->belongsToMany(User::class,'friend_request','sender_id','receiver_id');
        }
        
        public function friendRequestsReceived()
        {
            return $this->belongsToMany(User::class, 'friend_request', 'receiver_id','sender_id');
        }
        
        public function friends()
        {
            return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id');
        }
}
