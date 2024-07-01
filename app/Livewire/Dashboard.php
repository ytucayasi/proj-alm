<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Reservation;
use App\Models\Variation;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class Dashboard extends Component
{
  use WithPagination;
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

  /* Búsqueda */
  public $search = '';

  /* Empresas con Reservaciones */
  public $todayReservations = [];

  public function mount()
  {
    $this->startDate = Carbon::now()->startOfMonth(); // Por defecto, inicio del mes actual
    $this->endDate = Carbon::now()->endOfMonth(); // Por defecto, fin del mes actual
    $this->countProducts();
    $this->countReservations();
    $this->calculateProfits();
    $this->loadTodayCompanies();
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
  public function loadTodayCompanies()
  {
    $today = Carbon::now()->format('Y-m-d');
    $this->todayReservations = Reservation::with('companies.company')
      ->whereDate('execution_date', $today)
      ->get();
  }
/*   public function loadLowStockProducts()
  {
    return Variation::join('products', 'product_variations.product_id', '=', 'products.id')
      ->where('product_variations.quantity_base', '<=', 'products.stock_min')
      ->select('product_variations.*', 'products.name as product_name', 'products.stock_min', 'products.description', 'products.state')
      ->when($this->search, function ($query) {
        $query->where('products.name', 'like', '%' . $this->search . '%');
      })
      ->paginate(10);
  } */
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
    return view('dashboard', [
/*       'lowStockProducts' => $this->loadLowStockProducts() */
    ]);
  }
}
