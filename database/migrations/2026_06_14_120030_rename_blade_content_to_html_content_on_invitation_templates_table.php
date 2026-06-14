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
        Schema::table('invitation_templates', function (Blueprint $table) {
            $table->renameColumn('blade_content', 'html_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invitation_templates', function (Blueprint $table) {
            $table->renameColumn('html_content', 'blade_content');
        });
    }
};
