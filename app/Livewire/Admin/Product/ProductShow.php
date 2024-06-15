<?php

namespace App\Livewire\Admin\Product;

use App\Livewire\Forms\ProductForm;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Variation;
use Livewire\Component;
use Livewire\WithPagination;

class ProductShow extends Component
{
  use WithPagination;
  public ProductForm $form;
  public $searchVariation;
  public $searchInventory;
  public function mount($productId)
  {
    $this->form->setProduct(Product::findOrFail($productId));
  }
  public function render()
  {
    $variationsQuery = Variation::query()
      ->where("product_id", $this->form->product->id)
      ->when($this->searchVariation, function ($query) {
        $query->join('units', 'product_variations.unit_id', '=', 'units.id')
          ->where('units.abbreviation', 'like', '%' . $this->searchVariation . '%')
          ->select('product_variations.*'); // Selecciona solo las columnas de variations
      });

    $inventoriesQuery = Inventory::query()
      ->where("product_id", $this->form->product->id)
      ->when($this->searchInventory, function ($query) {
        $query->join('units', 'inventories.unit_id', '=', 'units.id')
          ->where('units.abbreviation', 'like', '%' . $this->searchInventory . '%')
          ->select('inventories.*'); // Selecciona solo las columnas de inventories
      })
      ->orderBy('created_at', 'desc');
    return view('livewire.admin.product.product-show', [
      'inventories' => $inventoriesQuery->paginate(5, pageName: 'inventories-page'),
      'variations' => $variationsQuery->paginate(5, pageName: 'variations-page'),
    ]);
  }
}
