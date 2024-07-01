<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationCompany extends Model
{
  use HasFactory;
  protected $table = 'reservation_companies';
  protected $guarded = [
    "id"
  ];
  public function company()
  {
    return $this->belongsTo(Company::class);
  }
  public function reservation()
  {
    return $this->belongsTo(Reservation::class);
  }
}