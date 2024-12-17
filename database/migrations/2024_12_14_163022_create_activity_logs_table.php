<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('checked_out_by'); // The user who performed the action
            $table->integer('user_id')->nullable(); // The user affected by the action
            $table->string('action'); // Action description (e.g., "assigned", "returned")
            $table->string('asset_tag')->nullable(); // The asset involved
            $table->string('purpose')->nullable(); // The purpose of assignment
            $table->timestamps();
    
            // Add foreign keys
            $table->foreign('checked_out_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
