<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LayananRecord extends Model
{
    use HasFactory;

    // Table name (optional if Laravel uses the default convention)
    protected $table = 'layanan_record';

    // Fillable fields to allow mass assignment
    protected $fillable = [
        'response_id',
        'user_name',
        'student_id',
        'group_name',
        'question_text',
        'response_value',
        'year_semester',
        'created_at',
    ];

    // Timestamps are optional since you're explicitly setting them
    public $timestamps = false;

    /**
     * Scope a query to get records by year and semester.
     */
    public function scopeOfYearSemester($query, $yearSemester)
    {
        return $query->where('year_semester', $yearSemester);
    }
}
