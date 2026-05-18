<?php

use App\Enums\LoanStatus;
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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('loan_number')->unique();
            $table->string('user_photo_snapshot')->nullable();
            $table->date('start_date')->nullable();
            $table->date('estimated_return_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default(LoanStatus::PENDING->value);
            $table->foreignId('user_id')->constrained();
            $table->foreignId('book_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
