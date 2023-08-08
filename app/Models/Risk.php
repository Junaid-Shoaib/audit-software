<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Risk extends Model
{
    use HasFactory;

    protected $fillable = [
        'classification', 'completeness', 'accuracy', 'cut_off', 'presentation_disclosure', 'overall', 'perform_materiality', 'account_id', 'company_id', 'year_id', 'enabled',
    ];
}
