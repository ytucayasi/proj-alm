<?php

namespace App\Livewire\Admin\Product;

use App\Livewire\Forms\ProductForm;
use App\Models\Category;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProductPage extends Component
{
  use WithPagination;
  use LivewireAlert;

  public $search = '';
  public $perPage;
  public ProductForm $form;
  public $viewMode;
  public $modalCreateOrUpdate = 'modal-create-or-update';

  public function mount()
  {
    $this->perPage = Cache::get('product_per_page', 10);
    $this->viewMode = Cache::get('product_view_mode', 'table');
  }

  public function updating($name, $value)
  {
    if ($name == "search") {
      $this->setPage(1);
    }
  }

  public function updatingPerPage($value)
  {
    Cache::put('product_per_page', $value);
    $this->resetPage();
  }

  public function updatedViewMode($value)
  {
    Cache::put('product_view_mode', $value);
    $this->resetPage();
  }

  public function openModal($modalName)
  {
    $this->resetValidation();
    $this->form->reset();
    $this->dispatch('open-modal', $modalName);
  }

  public function closeModal($modalName)
  {
    $this->resetValidation();
    $this->form->reset();
    $this->dispatch('close-modal', $modalName);
  }

  public function alertDelete($productId)
  {
    $this->confirm('¿Está seguro de eliminar el elemento?', [
      'onConfirmed' => 'delete',
      'confirmButtonText' => 'Aceptar',
      'cancelButtonText' => 'Cancelar',
      'data' => ['id' => $productId]
    ]);
  }

  public function view($productId)
  {
    $this->redirectRoute('products.show', ['productId' => $productId], navigate: true);
  }

  #[On('delete')]
  public function delete($data)
  {
    $this->form->setProduct(Product::findOrFail($data['id']));
    $this->form->delete();
    $this->alert('success', 'Producto eliminado con éxito');
  }

  public function save()
  {
    if ($this->form->product) {
      $this->form->update();
    } else {
      $this->form->store();
    }
    $this->alert('success', 'Se creó/actualizó con éxito');
    $this->closeModal($this->modalCreateOrUpdate);
  }

  public function edit($productId)
  {
    $this->resetValidation();
    $this->form->setProduct(Product::findOrFail($productId));
    $this->dispatch('open-modal', $this->modalCreateOrUpdate);
  }

  public function toggleViewMode()
  {
    $this->viewMode = $this->viewMode === 'table' ? 'grid' : 'table';
    $this->updatedViewMode($this->viewMode);
  }

  public function render()
  {
    return view('livewire.admin.product.product-page', [
      'products' => Product::where('name', 'like', '%' . $this->search . '%')
        ->paginate($this->perPage),
      'categories' => Category::where('state', 1)->get(),
    ]);
  }
}
