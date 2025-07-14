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
        Schema::create('perhitungan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('marketing_Id');
            $table->string('bulan');
            $table->bigInteger('omset');
            $table->decimal('komisi');
            $table->bigInteger('komisi_total');
            $table->timestamps();

            $table->foreign('marketing_Id')->references('id')->on('marketing');
            $table->unique(['marketing_Id', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perhitungan');
    }
};
