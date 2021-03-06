<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trials', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('distribution_interval');
            $table->integer('num_waves');
            $table->integer('num_players');
            $table->boolean('mult_factoid');
            $table->boolean('pay_correct');
            $table->integer('num_rounds');
            $table->boolean('is_active');
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
        Schema::drop('trials');
    }
}
