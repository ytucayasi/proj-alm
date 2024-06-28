<?php

namespace App\Livewire\Admin\Inventory;

use App\Livewire\Forms\InventoryForm;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryPage extends Component
{
  use WithPagination;
  use LivewireAlert;

  public $search = '';
  public $perPage;
  public InventoryForm $form;
  public $viewMode;
  public $modalCreateOrUpdate = 'modal-create-or-update';
  public $productSearch = '';

  public function mount()
  {
    $this->perPage = Cache::get('inventory_per_page', 10);
    $this->viewMode = Cache::get('inventory_view_mode', 'table');
  }

  public function updatingPerPage($value)
  {
    Cache::put('inventory_per_page', $value);
    $this->resetPage();
  }

  public function updatedViewMode($value)
  {
    Cache::put('inventory_view_mode', $value);
    $this->resetPage();
  }

  public function selectProduct($productId, $productName)
  {
    $this->form->product_id = $productId;
    $this->productSearch = $productName;
    $this->products = [];
  }
  public function searchProducts()
  {
    return Product::where('name', 'like', '%' . $this->productSearch . '%')->take(5)->get();
  }
  public function openModal($modalName)
  {
    $this->resetValidation();
    $this->form->reset();
    $this->productSearch = "";
    $this->dispatch('open-modal', $modalName);
  }

  public function closeModal($modalName)
  {
    $this->resetValidation();
    $this->form->reset();
    $this->productSearch = "";
    $this->dispatch('close-modal', $modalName);
  }

  public function alertDelete($inventoryId)
  {
    $this->confirm('¿Está seguro de eliminar el elemento?', [
      'onConfirmed' => 'delete',
      'confirmButtonText' => 'Aceptar',
      'cancelButtonText' => 'Cancelar',
      'data' => ['id' => $inventoryId]
    ]);
  }

  #[On('delete')]
  public function delete($data)
  {
    try {
      $this->form->setInventory(Inventory::findOrFail($data['id']));
      $this->form->delete();
      $this->alert('success', 'Inventario eliminado con éxito');
    } catch (\Exception $e) {
      $this->alert('error', $e->getMessage());
    }
  }

  public function save()
  {
    try {
      if ($this->form->inventory) {
        $this->form->update();
      } else {
        $this->form->store();
      }
      $this->alert('success', 'Se creó/actualizó con éxito');
      $this->resetValidation();
      $this->form->reset();
      $this->productSearch = "";
      /* $this->closeModal($this->modalCreateOrUpdate); */
    } catch (\Exception $e) {
      $this->alert('error', $e->getMessage());
    }
  }

  public function edit($inventoryId)
  {
    $this->resetValidation();
    $this->form->setInventory(Inventory::findOrFail($inventoryId));
    $this->dispatch('open-modal', $this->modalCreateOrUpdate);
  }

  public function toggleViewMode()
  {
    $this->viewMode = $this->viewMode === 'table' ? 'grid' : 'table';
    $this->updatedViewMode($this->viewMode);
  }

  public function reservation($reservationId)
  {
    $this->redirectRoute('reservations.form', ['reservationId' => $reservationId], navigate: true);
  }

  public function render()
  {
    $inventories = Inventory::query()
      ->when($this->search, function ($query) {
        $query->join('products', 'inventories.product_id', '=', 'products.id')
          ->where('products.name', 'like', '%' . $this->search . '%')
          ->select('inventories.*');
      })
      ->orderBy('id', 'desc');
    return view('livewire.admin.inventory.inventory-page', [
      'inventories' => $inventories->paginate($this->perPage),
      'products' => Product::where("state", 1)->get(),
      'units' => Unit::where("state", 1)->get(),
      'searchResults' => $this->searchProducts(),
    ]);
  }
}
