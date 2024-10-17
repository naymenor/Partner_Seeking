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
        Schema::create('customer_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->string('registration_fee')->nullable();
            $table->text('personal_infos')->nullable();
            $table->text('demographic_infos')->nullable();
            $table->text('educational_infos')->nullable();
            $table->text('employment_infos')->nullable();
            $table->text('marital_infos')->nullable();
            $table->text('referees_infos')->nullable();
            $table->text('preferance_infos')->nullable();
            $table->text('religious_infos')->nullable();
            $table->string('is_verified')->nullable();
            $table->string('created_by')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_profiles');
    }
};
