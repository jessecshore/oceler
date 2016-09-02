<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGroupIdToTrialUserArchive extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
     {
         Schema::table('trial_user_archive', function(Blueprint $table){

             $table->integer('group_id')->after('user_id')->unsigned();

             $table->foreign('group_id')
                   ->references('id')->on('groups')
                   ->onDelete('cascade');

         });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down()
     {
         Schema::table('trial_user_archive', function (Blueprint $table) {

           $table->dropForeign('trial_user_archive_group_id_foreign');
           $table->dropColumn('group_id');

         });
     }
}
