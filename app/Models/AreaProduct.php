<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AreaProduct extends Model
{
  use HasFactory;

  protected $table = 'area_product';

  protected $guarded = [
    "id"
  ];

  public function area()
  {
    return $this->belongsTo(Area::class);
  }

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function unit()
  {
    return $this->belongsTo(Unit::class);
  }
}
