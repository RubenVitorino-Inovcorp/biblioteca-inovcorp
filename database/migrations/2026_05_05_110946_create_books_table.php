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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('isbn')->unique();
            $table->string('title');
            $table->text('bibliography')->nullable();
            $table->string('image_path')->nullable();
            $table->decimal('price', 8, 2)->default(0.00);
            $table->foreignId('publisher_id')->nullable()
                                                    ->constrained()
                                                    ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
