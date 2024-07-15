<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
  use HasFactory;
  protected $guarded = [
    "id"
  ];

  public function inventories()
  {
    return $this->hasMany(Inventory::class);
  }

  public function companies()
  {
    return $this->hasMany(ReservationCompany::class);
  }

  public function reservationCompanies()
  {
    return $this->hasMany(ReservationCompany::class);
  }

  protected $casts = [
    'order_date' => 'datetime',
    'execution_date' => 'datetime',
    'status' => 'integer',
    'payment_status' => 'integer',
    'type' => 'integer',
  ];
}
