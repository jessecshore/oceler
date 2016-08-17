<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrRoundToTrialsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('trials', function (Blueprint $table) {

      $table->integer('curr_round')->after('num_rounds');

    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('trials', function (Blueprint $table) {

      $table->dropColumn('curr_round');

    });
  }
}
