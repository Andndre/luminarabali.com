<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('component_library');
    }

    public function down(): void
    {
        Schema::create('component_library', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category', 100);
            $table->text('description')->nullable();
            $table->string('thumbnail', 500)->nullable();
            $table->longText('code')->nullable();
            $table->json('variables')->nullable();
            $table->string('type', 50)->default('component');
            $table->boolean('is_public')->default(false);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index('category');
            $table->index('type');
            $table->index('is_public');
        });
    }
};
