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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedDecimal('amount', 9, 2)->default(0);
            $table->string('clafiya_reference')->unique()->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_reference')->unique()->nullable();
            $table->foreignUuid('appointment_id');
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('payment_date')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
