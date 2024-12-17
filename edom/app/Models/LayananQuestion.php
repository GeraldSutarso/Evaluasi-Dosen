<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananQuestion extends Model
{
    use HasFactory;
    protected $fillable = ['id',
        'text',
        'type',
    ];
    public $timestamps = false;
    public function layanan_responses()
    {
        return $this->hasMany(LayananResponse::class);
    }
    protected $table = 'layanan_questions';
}
