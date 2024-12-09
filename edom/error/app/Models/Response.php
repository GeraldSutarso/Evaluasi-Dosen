<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;
    public function evaluation() {
        return $this->belongsTo(Evaluation::class);
    }
    public function question() {
        return $this->belongsTo(Question::class);
    }
    protected $fillable = [
        'evaluation_id',
        'question_id',
        'response_value',
    ];
    protected $primaryKey = 'id';
    // public $incrementing = false;
    protected $table = 'responses';
}
