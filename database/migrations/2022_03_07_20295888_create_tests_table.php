<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->unsigned();

            $table->string('requested_test');
            $table->string('lab_reference')->nullable();
            $table->string('sample_collection_date')->nullable();
            $table->string('doctors_ref')->nullable();

            $table->string('payment_status')->default(0);
            $table->string('payment_mode');

            $table->foreignId('type_id')->unsigned();
            $table->foreignId('price_id')->unsigned();
            $table->foreignId('tube_id')->unsigned();

            $table->foreignId('sample_id')->unsigned();
            $table->string('sample_by')->nullable();
            $table->string('sample_status')->default(0);

            $table->string('mixed_status')->default(0);
            $table->string('mixed_by')->nullable();

            $table->string('controlled_status')->default(0);
            $table->string('controlled_by')->nullable();

            $table->string('results')->nullable();
            $table->string('comments')->nullable();
            $table->string('attachment')->nullable();
            $table->string('generated_by')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('testing_lab')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tests');
    }
}
