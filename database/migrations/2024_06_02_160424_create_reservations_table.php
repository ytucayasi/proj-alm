<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('reservations', function (Blueprint $table) {
      $table->id();
      $table->string('company_name');
      $table->text('description')->nullable(); 
      $table->tinyInteger('status')->default(3)->comment('Reservation status: 1=realized, 2=in execution, 3=pending, 4=canceled, 5=postponed'); // Reservation status
      $table->tinyInteger('payment_status')->default(2)->comment('Payment status: 1=paid, 2=payment pending'); 
      $table->tinyInteger('type')->default(1); 
      $table->datetime('order_date'); 
      $table->datetime('execution_date')->nullable(); 
      $table->decimal('total_cost', 10, 2); 
      $table->unsignedInteger('people_count')->comment('pack'); 
      $table->unsignedInteger('total_products'); 
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('reservations');
  }
};
