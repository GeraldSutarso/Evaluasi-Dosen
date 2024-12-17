<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananResponse extends Model
{
    use HasFactory;
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function layanan_question() {
        return $this->belongsTo(LayananQuestion::class);
    }
    protected $fillable = [
        'user_id',
        'question_id',
        'response_value',
    ];
    protected $primaryKey = 'id';
    // public $incrementing = false;
    protected $table = 'layanan_responses';
}
