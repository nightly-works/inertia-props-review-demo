<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('technician');
            $table->string('phone')->nullable();
            $table->string('home_address')->nullable();
            $table->integer('hourly_cost_cents')->default(0);
        });

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_email');
            $table->text('internal_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('site_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('technician_id')->constrained('users');
            $table->string('title');
            $table->date('scheduled_on');
            $table->integer('price_cents');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_jobs');
        Schema::dropIfExists('clients');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['role', 'phone', 'home_address', 'hourly_cost_cents']));
    }
};
