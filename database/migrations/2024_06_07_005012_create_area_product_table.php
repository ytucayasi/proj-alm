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
      $table->unsignedBigInteger('area_id');
      $table->unsignedBigInteger('product_id');
      $table->unsignedBigInteger('unit_id')->nullable();
      $table->decimal('quantity', 8, 2)->unsigned();
      $table->decimal('price', 10, 2)->unsigned();
      $table->tinyInteger('state')->default(1);
      $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');
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
    Schema::dropIfExists('area_product');
  }
};
