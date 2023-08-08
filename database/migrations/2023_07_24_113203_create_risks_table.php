<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRisksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();
            $table->string('classification');
            $table->string('completeness');
            $table->string('accuracy');
            $table->string('cut_off');
            $table->string('presentation_disclosure');
            $table->string('overall');
            $table->string('perform_materiality');
            $table->tinyInteger('enabled')->default('1');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('account_id');
            $table->unsignedBigInteger('year_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->foreign('year_id')->references('id')->on('years');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('risks');
    }
}
