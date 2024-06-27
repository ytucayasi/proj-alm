<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Inventory;
use App\Models\Reservation;
use Livewire\Component;

class Dashboard extends Component
{
  public $selectedProduct;
  public $totalQuantity = 0;
  public $totalRevenue = 0;
  public $totalReservations = 0;

  public function mount()
  {
    $this->updateTotalQuantity();
    $this->updateTotalRevenue();
    $this->updateTotalReservations();
  }

  public function updatedSelectedProduct()
  {
    $this->updateTotalQuantity();
    $this->updateTotalRevenue();
    $this->updateTotalReservations();
  }

  public function updateTotalQuantity()
  {
    if ($this->selectedProduct) {
      $totalQuantityIn = Inventory::where('product_id', $this->selectedProduct)
        ->where('movement_type', 1)
        ->sum('quantity');

      $totalQuantityOut = Inventory::where('product_id', $this->selectedProduct)
        ->where('movement_type', 2)
        ->sum('quantity');

      $this->totalQuantity = $totalQuantityIn - $totalQuantityOut;
    } else {
      $totalQuantityIn = Inventory::where('movement_type', 1)
        ->sum('quantity');

      $totalQuantityOut = Inventory::where('movement_type', 2)
        ->sum('quantity');

      $this->totalQuantity = $totalQuantityIn - $totalQuantityOut;
    }
  }

  public function updateTotalRevenue()
  {
    $entriesRevenue = Inventory::where('movement_type', 1)
      ->sum(\DB::raw('quantity * unit_price'));

    $reservationsCost = Inventory::where('movement_type', 2)
      ->where('type_action', 2)
      ->sum(\DB::raw('quantity * unit_price'));

    $this->totalRevenue = $entriesRevenue - $reservationsCost;
  }

  public function updateTotalReservations()
  {
    $this->totalReservations = Reservation::count();
  }

  public function render()
  {
    $products = Product::all();

    return view('dashboard', [
      'products' => $products,
      'totalQuantity' => $this->totalQuantity,
      'totalRevenue' => $this->totalRevenue,
      'totalReservations' => $this->totalReservations,
    ]);
  }
}
