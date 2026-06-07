<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospital_id')->constrained()->cascadeOnDelete();
            $table->string('blood_type');
            $table->integer('quantity')->default(0);
            $table->integer('min_threshold')->default(500);
            $table->timestamps();

            $table->unique(['hospital_id', 'blood_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
