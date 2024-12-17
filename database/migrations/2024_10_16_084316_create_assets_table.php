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
        Schema::create('assets', function (Blueprint $table) {
            $table->string('asset_tag')->primary();  // Use asset_tag as the primary key
            $table->unsignedInteger('device_type_id');
            $table->unsignedInteger('brand_id')->nullable();
            $table->string('model')->nullable();
            $table->string('serial_no')->nullable();
            $table->unsignedInteger('company_id');
            $table->string('condition');
            $table->string('status');
            $table->integer('user_id')->nullable();
            $table->integer('checked_out_by')->nullable();
            $table->string('purpose')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();

            // Set up foreign keys with the users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('checked_out_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict');
            $table->foreign('device_type_id')->references('id')->on('device_types')->onDelete('restrict');
            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
