<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->string('id_number')->nullable()->unique()->after('name');
            $table->date('birthday')->nullable()->after('id_number');
            $table->string('phone')->nullable()->after('birthday');
            $table->dateTime('joined_at')->nullable()->after('phone');
            $table->dateTime('expires_at')->nullable()->after('joined_at');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->dropUnique(['id_number']);
            $table->dropColumn(['id_number', 'birthday', 'phone', 'joined_at', 'expires_at']);
        });
    }
};
