<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 7D: template harus bisa dihapus meski masih dirujuk order lama tanpa
     * menggagalkan penghapusan atau konfirmasi lunas (lihat
     * OrderController::instantiateInvitationIfNeeded, yang sudah menoleransi
     * template null). restrictOnDelete() semula (7C) menghalangi skenario ini,
     * jadi dilonggarkan jadi nullable + nullOnDelete.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['invitation_template_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('invitation_template_id')->nullable()->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('invitation_template_id')
                ->references('id')->on('invitation_templates')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['invitation_template_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('invitation_template_id')->nullable(false)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('invitation_template_id')
                ->references('id')->on('invitation_templates')
                ->restrictOnDelete();
        });
    }
};
