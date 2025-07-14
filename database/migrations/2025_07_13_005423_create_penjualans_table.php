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
        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number');
            $table->unsignedBigInteger('marketing_Id');
            $table->date('date');
            $table->integer('cargo_fee');
            $table->integer('total_balance');
            $table->integer('grand_total');
            $table->timestamps();

            $table->foreign('marketing_Id')
            ->references('id')
            ->on('marketing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualan');
    }
};
