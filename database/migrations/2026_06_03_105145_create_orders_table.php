<?php

use App\Enums\OrderStatus;
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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('order_number')->unique();
            $table->decimal('total_price', 10, 2)->default(0.00);
            $table->text('delivery_address')->nullable();
            $table->string('status')->default(OrderStatus::PENDING->value);
            $table->string('stripe_session_id')->nullable()->unique();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
