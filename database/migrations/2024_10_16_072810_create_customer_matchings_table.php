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
        Schema::create('customer_matchings', function (Blueprint $table) {
            $table->id();
            $table->string('uuid');
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade')->nullable();
            $table->string('matchingUser')->nullable();
            $table->string('acceptBysender')->nullable();
            $table->string('acceptByreceiver')->nullable();
            $table->string('approveByAdmin')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_matchings');
    }
};
