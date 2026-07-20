<?php
// database/migrations/2026_07_22_000002_add_hero_slot_to_invitation_templates.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invitation_templates', function (Blueprint $table) {
            // string biasa, bukan enum: nilai divalidasi di aplikasi, dan enum
            // MySQL merepotkan saat slot bertambah.
            $table->string('hero_slot', 20)->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('invitation_templates', function (Blueprint $table) {
            $table->dropColumn('hero_slot');
        });
    }
};
