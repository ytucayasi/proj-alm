<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReservation extends Model
{
  use HasFactory;
  protected $guarded = [
    "id"
  ];

  public function reservation()
  {
    return $this->belongsTo(Reservation::class);
  }

}
