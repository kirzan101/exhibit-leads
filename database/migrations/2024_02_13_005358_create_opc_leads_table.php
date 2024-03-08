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
        Schema::create('opc_leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('companion_first_name')->nullable();
            $table->string('companion_middle_name')->nullable();
            $table->string('companion_last_name')->nullable();
            $table->string('address')->nullable();
            $table->string('hotel')->nullable();
            $table->string('mobile_number')->nullable();
            $table->string('occupation')->nullable();
            $table->string('age')->nullable();
            $table->string('source_prefix')->nullable();
            $table->string('source')->nullable();
            $table->string('civil_status')->nullable();
            $table->dateTime('date_filled')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opc_leads');
    }
};
