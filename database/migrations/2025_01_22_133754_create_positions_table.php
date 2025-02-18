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
        Schema::create('positions', function (Blueprint $table) {
            $table->increments('id'); // Auto-incrementing INT(11) ID (Primary key)
            $table->string('name'); // Name of the company
            $table->string('description')->nullable(); // Description of the company (nullable)
            $table->timestamps(); // Adds created_at and updated_at columns
        });
        // Insert into the table
        DB::table('positions')->insert([
            'name' => 'IT Technical Manager',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
