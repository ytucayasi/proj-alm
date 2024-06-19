<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Inventory;
use Livewire\Component;

class Dashboard extends Component
{
  public $selectedProduct;
  public $totalQuantity = 0;
  public $totalRevenue = 0;

  public function mount()
  {
    $this->updateTotalQuantity();
    $this->updateTotalRevenue();
  }

  public function updatedSelectedProduct()
  {
    $this->updateTotalQuantity();
    $this->updateTotalRevenue();
  }

  public function updateTotalQuantity()
  {
    if ($this->selectedProduct) {
      $this->totalQuantity = Inventory::where('product_id', $this->selectedProduct)
        ->where('movement_type', 1)
        ->sum('quantity');
    } else {
      $this->totalQuantity = Inventory::where('movement_type', 1)
        ->sum('quantity');
    }
  }

  public function updateTotalRevenue()
  {
    if ($this->selectedProduct) {
      $this->totalRevenue = Inventory::where('product_id', $this->selectedProduct)
        ->where('movement_type', 1)
        ->sum(\DB::raw('quantity * unit_price'));
    } else {
      $this->totalRevenue = Inventory::where('movement_type', 1)
        ->sum(\DB::raw('quantity * unit_price'));
    }
  }

  public function render()
  {
    $products = Product::all();

    return view('dashboard', [
      'products' => $products,
      'totalQuantity' => $this->totalQuantity,
      'totalRevenue' => $this->totalRevenue,
    ]);
  }
}