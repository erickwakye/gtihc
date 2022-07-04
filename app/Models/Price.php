<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'test',
        'amount',
        'vat',
        'tax',
        'levy'
    ];

    public function tests(){
        return $this->hasMany(Test::class);
    }


}
