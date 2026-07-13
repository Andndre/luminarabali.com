<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_assets', function (Blueprint $table) {
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('visibility')->default('private');
            $table->string('collection')->nullable();
        });

        // Aset lama: milik tim, uploader tidak diketahui (proposal §7).
        DB::table('invitation_assets')->update(['visibility' => 'team']);
    }

    public function down(): void
    {
        Schema::table('invitation_assets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('uploaded_by');
            $table->dropColumn(['visibility', 'collection']);
        });
    }
};
