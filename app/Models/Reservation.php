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

  protected $casts = [
    'order_date' => 'datetime',
    'execution_date' => 'datetime',
    'status' => 'integer',
    'payment_status' => 'integer',
    'type' => 'integer',
  ];
}