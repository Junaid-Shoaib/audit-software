<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;

    protected $fillable = [
        'begin', 'end','enabled','company_id','closed'
    ];

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'years_users');
    }

    public function company(){
        return $this->belongsTo('App\Models\Company', 'company_id');
    }

    public function entries()
    {
        return $this->hasMany('App\Models\Entry','year_id');
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Document','year_id');
    }

    //confirmation Relation

    public function bankBalances()
    {
        return $this->hasMany('App\Models\BankBalance', 'year_id');
    }

    public function bankConfirmations()
    {
        return $this->hasMany('App\Models\BankConfirmation', 'year_id');
    }
}
