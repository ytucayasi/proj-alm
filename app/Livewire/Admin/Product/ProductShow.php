<?php

namespace App\Livewire\Admin\Product;

use App\Livewire\Forms\ProductForm;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Variation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductShow extends Component
{
  use WithPagination;
  use LivewireAlert;
  public ProductForm $form;
  public $searchVariation;
  public $searchInventory;
  public $modalCreateOrUpdate = 'modal-create-or-update';
  public function mount($productId)
  {
    $this->form->setProduct(Product::findOrFail($productId));
  }
  public function openModal($modalName)
  {
    $this->resetValidation();
    $this->form->reset(['price', 'variationId']);
    $this->dispatch('open-modal', $modalName);
  }
  public function closeModal($modalName)
  {
    $this->resetValidation();
    $this->form->reset(['price', 'variationId']);
    $this->dispatch('close-modal', $modalName);
  }
  public function editVariation($variationId)
  {
    $this->form->setVariation($variationId);
    $this->dispatch('open-modal', $this->modalCreateOrUpdate);
  }
  public function saveVariation()
  {
    if ($this->form->variation) {
      $this->form->updateVariation();
    }
    $this->alert('success', 'Se actualizó el precio');
    $this->closeModal($this->modalCreateOrUpdate);
  }
  public function reservation($reservationId)
  {
    $this->redirectRoute('reservations.form', ['reservationId' => $reservationId], navigate: true);
  }
  public function render()
  {
    $variationsQuery = Variation::query()
      ->where("product_id", $this->form->product->id)
      ->when($this->searchVariation, function ($query) {
        $query->join('units', 'product_variations.unit_id', '=', 'units.id')
          ->where('units.abbreviation', 'like', '%' . $this->searchVariation . '%')
          ->select('product_variations.*'); // Selecciona solo las columnas de variations
      })->orderBy('id', 'desc');

    $inventoriesQuery = Inventory::query()
      ->where("product_id", $this->form->product->id)
      ->when($this->searchInventory, function ($query) {
        $query->join('units', 'inventories.unit_id', '=', 'units.id')
          ->where('units.abbreviation', 'like', '%' . $this->searchInventory . '%')
          ->select('inventories.*'); // Selecciona solo las columnas de inventories
      })
      ->orderBy('id', 'desc');
    return view('livewire.admin.product.product-show', [
      'inventories' => $inventoriesQuery->paginate(10, pageName: 'inventories-page'),
      'variations' => $variationsQuery->paginate(5, pageName: 'variations-page'),
    ]);
  }
}
