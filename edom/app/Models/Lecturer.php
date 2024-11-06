<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lecturer extends Model
{
    use HasFactory;
    public function matkuls() {
        return $this->hasMany(Matkul::class);
        
    }
    protected $fillable = [
        'name',
    ];
    protected $primaryKey = 'id';
    // public $incrementing = false;
    protected $table = 'lecturer';
}