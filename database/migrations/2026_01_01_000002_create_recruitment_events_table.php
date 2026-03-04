<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recruitment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recruiter_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('new_member_id')->constrained('members')->cascadeOnDelete();
            $table->unsignedInteger('company_income')->default(36000);
            $table->unsignedInteger('bonus_payout_total')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recruitment_events');
    }
};
