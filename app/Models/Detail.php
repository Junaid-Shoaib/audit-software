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

    public function account(){
        return $this->belongsTo('App\Models\Account', 'account_id');
    }



    public function accountgroup() {
        return $this->hasMany('App\Models\Account', 'account_id')
        ->groupBy('account_id')
        ->orderBy('account_id', 'desc');
    }
}

