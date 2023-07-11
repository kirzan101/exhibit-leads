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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('title');
            $table->string('alias')->nullable();
            $table->string('suffix')->nullable();
            $table->string('birth_date');
            $table->string('address');
            $table->string('secondary_address')->nullable();
            $table->string('nationality');
            $table->string('gender');
            $table->string('civil_status');
            $table->string('company_name');
            $table->string('company_number')->nullable();
            $table->string('occupation');
            $table->string('email');
            $table->string('mobile_number_one');
            $table->string('mobile_number_two')->nullable();
            $table->string('telephone')->nullable();
            $table->string('fax')->nullable();
            $table->string('combined_monthly_income');
            $table->string('internet_connection');
            $table->string('owned_gadgets');
            $table->string('other_gadgets')->nullable();
            $table->string('spouse_occupation');
            $table->string('nature_of_business');
            $table->string('property_assigned');
            $table->string('contract_file')->nullable();
            $table->boolean('is_assigned')->default(false);
            $table->longText('remarks')->nullable();
            $table->boolean('is_invited')->default(false);
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('created_by')->references('id')->on('employees');
            $table->foreign('updated_by')->references('id')->on('employees');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
