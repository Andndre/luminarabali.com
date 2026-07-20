<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Nullable: order lama (7C) belum punya undangan. nullOnDelete:
            // menghapus InvitationPage tak boleh menghapus riwayat order.
            $table->foreignId('invitation_page_id')->nullable()
                ->after('confirmed_by')
                ->constrained('invitation_pages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('invitation_page_id');
        });
    }
};
