<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [


            'patient_id',

            'requested_test',
            'lab_reference',
            'sample_collection_date',
            'doctors_ref',

            'payment_status',
            'payment_mode',
            'batch',

            'type_id',
            'price_id',
            'tube_id',

            'sample_id',
            'sample_by',
            'sample_status',

            'mixed_status',
            'mixed_by',

            'controlled_status',
            'controlled_by',

            'results',
            'comments',
            'attachment',
            'generated_by',
            'approved_by',
            'testing_lab',
    ];

    public function patient(){
        return $this->belongsTo(Patient::class);
    }

    public function price(){
        return $this->belongsTo(Price::class);
    }

    public function sample(){
        return $this->belongsTo(Sample::class);
    }

}
