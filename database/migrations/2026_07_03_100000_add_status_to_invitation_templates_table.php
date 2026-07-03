<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_templates', function (Blueprint $table) {
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft')->after('category');
        });

        DB::table('invitation_templates')->where('is_active', true)->update(['status' => 'published']);
        DB::table('invitation_templates')->where('is_active', false)->update(['status' => 'draft']);

        Schema::table('invitation_templates', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropColumn('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_templates', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('category');
        });

        DB::table('invitation_templates')->where('status', 'published')->update(['is_active' => true]);
        DB::table('invitation_templates')->where('status', '!=', 'published')->update(['is_active' => false]);

        Schema::table('invitation_templates', function (Blueprint $table) {
            $table->index('is_active');
            $table->dropColumn('status');
        });
    }
};
