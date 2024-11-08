<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_matkul')->withPivot('week_number');
    }

    public function lecturers()
    {
        return $this->belongsToMany(Lecturer::class, 'lecturer_matkul');
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }
    protected $table = 'matkuls';
}
