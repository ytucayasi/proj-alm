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
    Schema::create('reservation_companies', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('reservation_id');
      $table->unsignedBigInteger('company_id');
      $table->integer('pack');
      $table->decimal('cost_pack', 8, 2);
      $table->decimal('total_pack', 8, 2);
      $table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
      $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('reservation_companies');
  }
};
