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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('title');
            $table->string('alias')->nullable();
            $table->string('suffix')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('address');
            $table->string('secondary_address')->nullable();
            $table->string('nationality')->nullable();
            $table->string('gender')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_number')->nullable();
            $table->string('occupation')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_number_one');
            $table->string('mobile_number_two')->nullable();
            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('combined_monthly_income')->nullable();
            $table->string('internet_connection')->nullable();
            $table->string('owned_gadgets')->nullable();
            $table->string('other_gadgets')->nullable();
            $table->string('spouse_first_name')->nullable();
            $table->string('spouse_last_name')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->string('nature_of_business')->nullable();
            $table->unsignedBigInteger('property_id');
            $table->string('contract_file')->nullable();
            $table->boolean('is_done')->default(false);
            $table->boolean('is_done_confirmed')->default(false);
            $table->boolean('is_booker_assigned')->default(false);
            $table->boolean('is_confirm_assigned')->default(false);
            $table->boolean('is_exhibitor_assigned')->default(false);
            $table->longText('remarks')->nullable();
            $table->string('lead_status')->nullable();
            $table->longText('confirmer_remarks')->nullable();
            $table->string('lead_status_confirmer')->nullable();
            $table->string('exhibit_code')->nullable();
            $table->string('source_prefix')->nullable();
            $table->string('source')->nullable();
            $table->date('presentation_date')->nullable();
            $table->time('presentation_time')->nullable();
            $table->string('refer_by')->nullable();
            $table->string('holiday_consultant')->nullable();
            $table->string('membership_type')->nullable();
            $table->boolean('is_confidential')->default(false);
            $table->boolean('is_showed')->default(false);
            $table->unsignedBigInteger('venue_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('venue_id')->references('id')->on('venues');
            $table->foreign('property_id')->references('id')->on('properties');
            $table->foreign('created_by')->references('id')->on('employees');
            $table->foreign('updated_by')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
