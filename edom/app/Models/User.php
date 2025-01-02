<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'student_id',
        'group_id'
    ];
    public $timestamps = false;
    
        public function group()
    {
        return $this->belongsTo(Group::class);
    }
    protected $table = 'users';
    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
    public function layananResponses()
    {
        return $this->hasMany(LayananResponse::class);
    }
    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    
}
