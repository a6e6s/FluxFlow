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
        Schema::table('users', function (Blueprint $table) {
            $table->text('api_key')->nullable()->after('remember_token');
            $table->string('api_key_hash', 64)->nullable()->unique()->after('api_key');
            $table->timestamp('api_key_generated_at')->nullable()->after('api_key_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_api_key_hash_unique');
            $table->dropColumn(['api_key', 'api_key_hash', 'api_key_generated_at']);
        });
    }
};
