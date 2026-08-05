<?php

use App\Enums\Ask;
use App\Enums\OrderType;
use App\Enums\PaymentStatus;
use App\Enums\PaymentGateway;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_serial_no')->nullable();
            $table->string('token')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('restaurant_id')->constrained('restaurants');
            $table->decimal('subtotal', 19, 6);
            $table->decimal('discount', 19, 6)->nullable()->default(0);
            $table->decimal('delivery_fee', 19, 6)->nullable()->default(0);
            $table->decimal('total_tax', 19, 6)->nullable()->default(0);
            $table->decimal('total', 19, 6);
            $table->tinyInteger('order_type')->default(OrderType::DELIVERY);
            $table->dateTime('order_datetime')->useCurrent();
            $table->string('delivery_time')->nullable();
            $table->integer('preparation_time')->default(0);
            $table->tinyInteger('is_advance_order')->default(Ask::YES);
            $table->bigInteger('payment_method')->default(PaymentGateway::CASH_ON_DELIVERY);
            $table->tinyInteger('payment_status')->default(PaymentStatus::UNPAID);
            $table->tinyInteger('cutlery')->default(Ask::NO);
            $table->tinyInteger('status');
            $table->bigInteger('delivery_boy_id')->nullable();
            $table->tinyInteger('is_received')->default(Ask::NO);
            $table->tinyInteger('delivery_boy_request')->default(Ask::YES);
            $table->decimal('service_fee', 19, 6)->default(0);
            $table->decimal('rider_tip', 19, 6)->default(0);
            $table->text('reason')->nullable();
            $table->string('source')->nullable();
            $table->tinyInteger('active')->default(Ask::NO);
            $table->decimal('extra_delivery_fee', 19, 6)->nullable();
            $table->string('creator_type')->nullable();
            $table->bigInteger('creator_id')->nullable();
            $table->string('editor_type')->nullable();
            $table->bigInteger('editor_id')->nullable();
            $table->timestamps();
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
