<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('display_id')->constrained('displays')->cascadeOnDelete();
            $table->string('title');
            $table->json('custom_elements')->nullable();
            $table->enum('status', ['draft', 'active', 'expired'])->default('draft');
            $table->date('display_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['display_id', 'status']);
            $table->index(['display_date', 'start_time', 'end_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contents');
    }
};