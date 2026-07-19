<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_sections', function (Blueprint $table) {
            $table->json('variant_thumbnails')->nullable()->after('props');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_sections', function (Blueprint $table) {
            $table->dropColumn('variant_thumbnails');
        });
    }
};
