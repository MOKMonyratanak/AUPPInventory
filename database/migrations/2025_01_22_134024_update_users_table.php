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
        Schema::table('users', function (Blueprint $table) {

            // Change 'id' column to remove auto-increment
            $table->integer('id')->change();

            // Add new fields
            $table->enum('role', ['user', 'manager', 'admin'])->after('name'); 
            $table->unsignedInteger('company_id')->after('role'); 
            $table->unsignedInteger('position_id')->after('company_id'); 
            $table->string('contact_number')->after('position_id'); 
            $table->enum('status', ['employed', 'resigned'])->after('contact_number'); 

            // Make 'password' column nullable
            $table->string('password')->nullable()->change();

            // Define foreign key relationship
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict');
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('restrict');
        });

        // Insert a new user into the users table
        DB::table('users')->insert([
            'id' => '1',
            'name' => 'Administrator', // Name of the user
            'email' => 'admin@example.com', // Email of the user
            'password' => Hash::make('00000000'), // Hashed password
            'role' => 'admin', // Role
            'company_id' => 1, // Optional: Assign company ID if applicable
            'position_id' => 1, // Position
            'contact_number' => '000000000', // Contact number
            'status' => 'employed', // Status
            'created_at' => now(), // Timestamps
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key before dropping the column
            $table->dropForeign(['company_id']);
            $table->dropForeign(['position_id']);

            // Drop the newly added fields
            $table->dropColumn('role');
            $table->dropColumn('company_id');
            $table->dropColumn('position_id');
            $table->dropColumn('contact_number');
            $table->dropColumn('status');

            // Revert 'id' back to auto-increment
            $table->bigIncrements('id')->change();
            // Revert 'password' to non-nullable
            $table->string('password')->nullable(false)->change();
        });

        // Delete the inserted user during rollback
        DB::table('users')->where('email', 'admin@gmail.com')->delete();
    }
};
