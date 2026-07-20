<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_pages', function (Blueprint $table) {
            // Pemilik customer, terpisah dari created_by (mitra/admin pembuat).
            // Nullable: undangan lama tak punya owner (dianggap milik admin).
            $table->foreignId('owner_id')->nullable()->after('created_by')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invitation_pages', function (Blueprint $table) {
            $table->dropConstrainedForeignId('owner_id');
        });
    }
};
