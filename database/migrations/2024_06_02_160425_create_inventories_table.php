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
      $table->unsignedBigInteger('unit_id')->nullable();
      $table->tinyInteger('movement_type')->comment('1: Entrada, 2: Salida');;
      $table->integer('quantity');
      $table->decimal('unit_price', 8, 2);
      $table->text('description')->nullable();
      $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
      $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
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
