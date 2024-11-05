<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
    public function student() {
        return $this->belongsTo(User::class, 'student_id');
    }
    public function responses() {
        return $this->hasMany(Response::class);
    }
    public function matkuls() {
        return $this->belongsTo(Matkul::class);
    }
    protected $fillable = [
        'class_id',
        'student_id',
        'completed',
        'submit_time',
    ];
    protected $primaryKey = 'id';
    // public $incrementing = false;
    protected $table = 'evaluation';
    
}
