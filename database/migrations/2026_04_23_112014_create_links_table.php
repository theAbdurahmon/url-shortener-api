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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained()->cascadeOnDelete();
            $table->text("original_url");
            $table->string("slug")->unique();
            $table->string("title")->nullable();
            $table->timestamp("expires_at")->nullable();
            $table->boolean("is_active");
            $table->string("password")->nullable();
            $table->integer("click_limit")->nullable();
            $table->integer("clicks_count");
            $table->timestamp("created_at")->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
