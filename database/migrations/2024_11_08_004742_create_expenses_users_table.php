<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cards_users_id')->nullable();
            $table->foreign('cards_users_id')->references('id')->on('cards_users');
            $table->string('description')->comment('Expense Description');
            $table->float('expense');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses_users');
    }
};
