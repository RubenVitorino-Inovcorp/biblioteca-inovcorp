<?php

use App\Enums\CartStatus;
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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->boolean('has_notified')->default(false);
            $table->string('cart_number')->unique();
            $table->string('status')->default(CartStatus::ACTIVE->value);
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->index(['updated_at', 'has_notified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
