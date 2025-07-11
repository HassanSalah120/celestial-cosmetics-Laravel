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
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
            $table->string('customer_role')->nullable()->after('customer_name_ar');
            $table->string('customer_role_ar')->nullable()->after('customer_role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn('avatar');
            $table->dropColumn('customer_role');
            $table->dropColumn('customer_role_ar');
        });
    }
};
