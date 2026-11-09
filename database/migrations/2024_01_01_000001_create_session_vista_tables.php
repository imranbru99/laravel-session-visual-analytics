<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_vista_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 191)->unique();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('user_email')->nullable()->index();
            $table->string('user_name')->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('device_type', 32)->nullable()->index(); // desktop|mobile|tablet|bot|unknown
            $table->string('browser', 64)->nullable()->index();
            $table->string('platform', 64)->nullable();
            $table->string('country', 64)->nullable()->index();
            $table->string('city', 96)->nullable();
            $table->string('status', 24)->default('online')->index(); // online|idle|expired
            $table->string('landing_path', 255)->nullable();
            $table->string('current_path', 255)->nullable()->index();
            $table->unsignedInteger('page_views')->default(0);
            $table->timestamp('first_seen_at')->nullable()->index();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamp('ended_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('session_vista_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_vista_session_id')
                ->constrained('session_vista_sessions')
                ->cascadeOnDelete();
            $table->string('type', 64)->index(); // pageview|heartbeat|login|logout|custom
            $table->string('path', 255)->nullable()->index();
            $table->string('method', 16)->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_vista_events');
        Schema::dropIfExists('session_vista_sessions');
    }
};
