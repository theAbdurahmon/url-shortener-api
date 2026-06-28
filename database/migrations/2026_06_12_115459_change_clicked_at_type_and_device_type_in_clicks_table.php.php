<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        DB::table('clicks')->where('device_type', 'mobile')->update(['device_type' => 'phone']);

        Schema::table('clicks', function (Blueprint $table) {
            DB::statement('ALTER TABLE "clicks" DROP CONSTRAINT IF EXISTS clicks_device_type_check');

            DB::statement('ALTER TABLE "clicks" ADD CONSTRAINT clicks_device_type_check CHECK (device_type IN (\'desktop\', \'phone\', \'tablet\'))');
        });
    }

    public function down(): void
    {
        DB::table('clicks')->where('device_type', 'phone')->update(['device_type' => 'mobile']);

        Schema::table('clicks', function (Blueprint $table) {
            DB::statement('ALTER TABLE "clicks" DROP CONSTRAINT IF EXISTS clicks_device_type_check');
            DB::statement('ALTER TABLE "clicks" ADD CONSTRAINT clicks_device_type_check CHECK (device_type IN (\'desktop\', \'mobile\', \'tablet\'))');
        });
    }
};
