<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('type_users_id')->nullable();
            $table->foreign('type_users_id')->references('id')->on('type_users');
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
    
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['type_users_id']);
            $table->dropColumn('type_users_id');
        });

    }

}

