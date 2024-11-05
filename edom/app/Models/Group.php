<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function matkuls()
    {
        return $this->belongsToMany(Matkul::class, 'group_matkul')
                    ->withPivot('week_number'); // Tracks weekly attendance
    }
}
