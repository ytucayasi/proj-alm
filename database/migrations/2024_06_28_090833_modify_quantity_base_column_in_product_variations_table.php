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
    Schema::table('product_variations', function (Blueprint $table) {
      $table->decimal('quantity_base', 8, 2)->change();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('product_variations', function (Blueprint $table) {
      $table->unsignedBigInteger('quantity_base')->change();
    });
  }
};
