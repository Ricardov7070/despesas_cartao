<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCardsUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        
        Schema::table('cards_users', function (Blueprint $table) {
            $table->unsignedBigInteger('number')->change(); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {

        Schema::table('cards_users', function (Blueprint $table) {
            $table->integer('number')->change();
        });
   
    }

}

