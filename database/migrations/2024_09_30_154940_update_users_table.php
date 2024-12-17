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

            // Add new fields
            $table->string('role')->after('name'); 
            $table->unsignedInteger('company_id')->nullable()->after('role'); 
            $table->string('position')->nullable()->after('company_id'); 
            $table->string('contact_number')->nullable()->after('position'); 
            $table->string('status')->after('contact_number'); 

            // Make 'password' column nullable
            $table->string('password')->nullable()->change();

            // Define foreign key relationship
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key before dropping the column
            $table->dropForeign(['company_id']);

            // Drop the newly added fields
            $table->dropColumn('role');
            $table->dropColumn('company_id');
            $table->dropColumn('position');
            $table->dropColumn('contact_number');
            $table->dropColumn('status');

            // Revert 'password' to non-nullable
            $table->string('password')->nullable(false)->change();
        });
    }
};
