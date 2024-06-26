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
    Schema::create('inventories', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('unit_id'); // cambio reciente era "nullable"
      $table->unsignedBigInteger('reservation_id')->nullable(); // cambio reciente era "nullable"
      $table->tinyInteger('movement_type')->comment('1: Entrada, 2: Salida');
      $table->decimal('quantity', 8, 2)->unsigned();
      $table->decimal('unit_price', 8, 2);
      $table->tinyInteger('type_action')->default(1)->comment('1: Normal, 2: Reserva');
      $table->tinyInteger('status')->default(1)->comment('1: Relación, 2: Sin Relación');
      $table->tinyInteger('type')->default(1)->comment('1: Inventario, 2: Activo');
      $table->unsignedBigInteger('type_area')->default(1)->comment('ID del Area Seleccionada');
      $table->text('description')->nullable();
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
      $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade'); // cambio reciente era "set null"
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('inventories');
  }
};
