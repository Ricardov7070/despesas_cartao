<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertTableTypeUsers extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {

        DB::table('type_users')->insert([
            ['description' => 'Administrator', 'created_at' => now(), 'updated_at' => now()],
            ['description' => 'Standard', 'created_at' => now(), 'updated_at' => now()],
        ]);

    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {

        DB::table('type_users')
            ->where('description', 'Administrador')
            ->orWhere('description', 'Padrao')
            ->delete();

    }

}
