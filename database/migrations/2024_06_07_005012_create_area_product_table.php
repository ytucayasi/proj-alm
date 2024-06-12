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
    Schema::create('area_product', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('area_id')->nullable();
      $table->unsignedBigInteger('product_id')->nullable();
      $table->unsignedBigInteger('unit_id')->nullable();
      $table->unsignedBigInteger('quantity');
      $table->decimal('price', 10, 2)->unsigned();
      $table->tinyInteger('state')->default(1);
      $table->foreign('area_id')->references('id')->on('areas')->onDelete('set null');
      $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
      $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('area_product');
  }
};
