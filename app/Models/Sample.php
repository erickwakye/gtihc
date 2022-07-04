<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description'
    ];

    public function tests(){
        return $this->hasMany(Test::class);
    }
}
