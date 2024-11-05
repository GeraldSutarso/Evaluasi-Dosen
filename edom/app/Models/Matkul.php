<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Matkul extends Model
{
    use HasFactory;
    public function lecturer() {
        return $this->belongsTo(Lecturer::class);
    }
    public function evaluations() {
        return $this->hasMany(Evaluation::class);
    }
    protected $fillable = [
        'name',
        'lecturer_id',
    ];
    protected $primaryKey = 'id';
    // public $incrementing = false;
    protected $table = 'matkuls';
}
