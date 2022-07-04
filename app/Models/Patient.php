<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
            'customerId',
            'first_name',
            'last_name',
            'middle_name',
            'gender',
            'address',
            'email',
            'mobile',
            'id_type',
            'id_number',
            'alt_mobile',
            'digital_address',
    ];

    public function tests(){
        return $this->hasMany(Test::class);
    }

}
