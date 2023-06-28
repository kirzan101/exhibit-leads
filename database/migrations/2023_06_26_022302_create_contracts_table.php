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
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->string('document_code');
            $table->date('contract_date');
            $table->decimal('list_price');
            $table->integer('payment_terms');
            $table->decimal('downpayment');
            $table->decimal('maintenance_fee');
            $table->integer('years_of_occupancy');
            $table->integer('home_resort');
            $table->integer('holiday_credits');
            $table->string('plan');
            $table->string('membership_type');
            $table->string('room_type')->nullable();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->string('reason_for_purchase')->nullable();
            $table->string('remarks')->nullable();
            $table->string('deck')->nullable();
            $table->string('relation_manager_id')->nullable();
            $table->string('venue_manager_id')->nullable();
            $table->string('holiday_consultant_id')->nullable();
            $table->integer('created_by');
            $table->integer('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
