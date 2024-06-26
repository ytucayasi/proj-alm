<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
  use HasFactory;
  protected $guarded = [
    "id"
  ];
  public function variations()
  {
    return $this->hasMany(Variation::class);
  }
  public function inventories()
  {
    return $this->hasMany(Inventory::class);
  }
}
