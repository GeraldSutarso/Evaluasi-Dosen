<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;
    
    protected $fillable = ['id','name' ,'type'];
    public $timestamps = false;
    public function matkuls()
    {
        return $this->belongsToMany(Matkul::class, 'lecturer_matkul');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
    protected $table = 'lecturers';
}
