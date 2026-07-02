<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_pages', function (Blueprint $table) {
            $table->json('theme_overrides')->nullable()->after('meta_data');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_pages', function (Blueprint $table) {
            $table->dropColumn('theme_overrides');
        });
    }
};
