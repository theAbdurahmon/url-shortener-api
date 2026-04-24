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
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId("link_id")->constrained()->cascadeOnDelete();
            $table->string("ip_address");
            $table->string("country", 2);
            $table->string("city");
            $table->enum("device_type", ["desktop", "mobile", "tablet"]);
            $table->string("browser");
            $table->string("os");
            $table->text("referer");
            $table->timestamp("clicked_at");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clicks');
    }
};
