<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'text',
        'type',
    ];
    public function responses()
    {
        return $this->hasMany(Response::class);
    }
    protected $table = 'questions';
}
