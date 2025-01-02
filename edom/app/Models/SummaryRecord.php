<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SummaryRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'semester',
        'mengetahui',
        'mengetahui_name',
        'kaprodi_tpmo',
        'kaprodi_topkr',
        'edom_lock',
        'layanan_lock',
    ];
}
