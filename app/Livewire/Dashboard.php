<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Reservation;
use App\Models\Variation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Pagination\Paginator;

class Dashboard extends Component
{
  use WithPagination;

  /* Properties */
  public $countProducts = 0;
  public $countInventory = 0;
  public $countActive = 0;
  public $countReservations = 0;
  public $countReservationR = 0;
  public $countReservationE = 0;
  public $countReservationPo = 0;
  public $countReservationPe = 0;
  public $total_profits = 0;
  public $total_percentage = 0;
  public $startDate;
  public $endDate;
  public $todayReservations = [];
  public $countProductsTotals = 0;

  /* Variables para Stock Mínimo */
  public $search = '';
  public $perPage = 10;

  public function mount()
  {
    $this->startDate = Carbon::now()->startOfMonth();
    $this->endDate = Carbon::now()->endOfMonth();
    $this->perPage = Cache::get('stock_min_per_page', 10);
    $this->countProducts();
    $this->countReservations();
    $this->calculateProfits();
    $this->loadTodayCompanies();
  }

  public function updating($name, $value)
  {
    if ($name == "search") {
      $this->setPage(1);
    }
  }

  public function updatingPerPage($value)
  {
    Cache::put('stock_min_per_page', $value);
    $this->resetPage();
  }
  public function countProducts()
  {
    $this->countProducts = Product::count();
    $this->countInventory = Product::where('product_type', 1)->count();
    $this->countActive = Product::where('product_type', 2)->count();
    $this->countProductsTotals = Variation::sum('quantity_base');
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
  public function render()
  {
    $variations = Variation::whereHas('product', function ($query) {
      $query
        ->where('product_type', '=', 1)
        ->when($this->search, function ($query, $search) {
          return $query->where('name', 'like', '%' . $search . '%');
        });
    })
      ->with('product')
      ->get();

    $sortedVariations = $variations->sortBy(function ($variation) {
      return $variation->product->name;
    });

    $currentPage = Paginator::resolveCurrentPage();
    $perPage = $this->perPage;
    $currentItems = $sortedVariations->slice(($currentPage - 1) * $perPage, $perPage)->all();

    $paginatedVariations = new Paginator($currentItems, $perPage, $currentPage);

    return view('dashboard', [
      'lowStockVariations' => $paginatedVariations,
    ]);
  }
}