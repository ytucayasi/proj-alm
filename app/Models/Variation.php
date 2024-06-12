<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variation extends Model
{
  use HasFactory;
  protected $guarded = [
    "id"
  ];
  protected $table = 'product_variations';
  public function unit()
  {
    return $this->belongsTo(Unit::class);
  }
  public function product()
  {
    return $this->belongsTo(Product::class);
  }
}
