<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('details', function (Blueprint $table) {
            $table->id();
            $table->date('date')->nullable();
            $table->longText('description')->nullable();
            $table->string('cheque')->nullable();
            $table->string('voucher_no')->nullable();
            $table->decimal('amount',14,2)->nullable();
            $table->tinyInteger('cash')->default(0);
            $table->tinyInteger('bank')->default(0);
            $table->tinyInteger('adjustment')->default(0);
            $table->tinyInteger('a')->default(0);
            $table->tinyInteger('b')->default(0);
            $table->tinyInteger('c')->default(0);
            $table->tinyInteger('d')->default(0);
            $table->tinyInteger('e')->default(0);
            $table->tinyInteger('f')->default(0);
            $table->longText('remark')->default(0);
            $table->tinyInteger('enabled')->default('1');
            $table->unsignedBigInteger('company_id');
            $table->unsignedBigInteger('year_id');
            $table->unsignedBigInteger('account_id');
            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('year_id')->references('id')->on('years');
            $table->foreign('account_id')->references('id')->on('accounts');
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
        Schema::dropIfExists('details');
    }
}
