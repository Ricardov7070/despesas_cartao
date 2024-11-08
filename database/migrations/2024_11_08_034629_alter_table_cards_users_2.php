<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCardsUsers2 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        
        Schema::table('cards_users', function (Blueprint $table) {
            $table->float('balance_used'); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {

        Schema::table('cards_users', function (Blueprint $table) {
            $table->dropColumn('balance_used');
        });
   
    }

}