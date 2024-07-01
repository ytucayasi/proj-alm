<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Reservation;
use Illuminate\Support\Carbon;
use Livewire\Component;

class Dashboard extends Component
{
  /* Products */
  public $countProducts = 0;
  public $countInventory = 0;
  public $countActive = 0;
  /* Reservation */
  public $countReservations = 0;
  public $countReservationR = 0; // Realizados
  public $countReservationE = 0; // En Ejecución
  public $countReservationPo = 0; // Pospuestos
  public $countReservationPe = 0; // Pendientes

  /* Ganancias */
  public $total_profits = 0;
  public $total_percentage = 0;

  /* Fecha */
  public $startDate;
  public $endDate;

  public function mount()
  {
    $this->startDate = Carbon::now()->startOfMonth(); // Por defecto, inicio del mes actual
    $this->endDate = Carbon::now()->endOfMonth(); // Por defecto, fin del mes actual
    $this->countProducts();
    $this->countReservations();
    $this->calculateProfits();
  }
  public function countProducts()
  {
    $this->countProducts = Product::count();
    $this->countInventory = Product::where('product_type', 1)->count();
    $this->countActive = Product::where('product_type', 2)->count();
  }
  public function countReservations()
  {
    $this->countReservations = Reservation::count();
    $this->countReservationR = Reservation::where('status', 1)->count();
    $this->countReservationE = Reservation::where('status', 2)->count();
    $this->countReservationPo = Reservation::where('status', 3)->count();
    $this->countReservationPe = Reservation::where('status', 4)->count();
  }
  public function calculateProfits()
  {
    $reservations = Reservation::whereBetween('order_date', [$this->startDate, $this->endDate])->get();

    $total_cost = $reservations->sum('total_cost');
    $total_pack = $reservations->sum('total_pack');
    $this->total_profits = $total_pack - $total_cost;

    if ($total_pack > 0) {
      $this->total_percentage = ($this->total_profits / $total_pack) * 100;
    } else {
      $this->total_percentage = 0;
    }

    // Formatear los valores para la salida
    $this->total_profits = number_format($this->total_profits, 2);
    $this->total_percentage = number_format($this->total_percentage, 2);
  }

  public function updatedStartDate()
  {
    $this->calculateProfits();
  }

  public function updatedEndDate()
  {
    $this->calculateProfits();
  }
  public function render()
  {
    return view('dashboard');
  }
}
