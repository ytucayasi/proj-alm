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
    Schema::create('products', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('category_id')->nullable();
      $table->string('name', 150)->unique();
      /* $table->decimal('stock_min', 8, 2)->default(5); */
      $table->text('description')->nullable();
      $table->tinyInteger('state')->comment('1: Disponible, 2: Agotado');
      $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('products');
  }
};
