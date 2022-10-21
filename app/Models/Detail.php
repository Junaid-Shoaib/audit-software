<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'year_id', 'account_id', 'date', 'description', 'cheque', 'voucher_no', 'amount', 'cash', 'bank', 'adjustment', 'a', 'b', 'c', 'd', 'e', 'f', 'remark', 'conclusion',
    ];

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function year(){
        return $this->belongsTo('App\Models\Year', 'year_id');
    }
}

