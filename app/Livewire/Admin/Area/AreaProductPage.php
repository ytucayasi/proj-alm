<?php

namespace App\Livewire\Admin\Area;

use App\Livewire\Forms\AreaProductForm;
use App\Models\Area;
use App\Models\AreaProduct;
use App\Models\Product;
use App\Models\Unit;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class AreaProductPage extends Component
{
  use WithPagination;
  use LivewireAlert;
  public $search = '';
  public $perPage = 10;
  public $modalCreateOrUpdate = 'modal-create-or-update';
  public AreaProductForm $form;
  public $productSearch = '';
  public function mount($areaId)
  {
    $this->form->setArea($areaId);
    $this->form->init();
  }
  public function updating($property, $value)
  {
    if ($property == "form.product_id") {
      $this->form->units = [];
      $this->form->unit_id = null;
      $this->form->price = 0;
    }
  }
  public function searchProducts()
  {
    return Product::where('name', 'like', '%' . $this->productSearch . '%')
      ->where("product_type", 1)
      ->whereHas('variations', function ($query) {
        $query->where('quantity_base', '>', 0);
      })
      ->orderBy('name', 'asc')
      ->take(5)
      ->get();
  }
  public function selectProduct($productId, $productName)
  {
    $this->form->product_id = $productId;
    $this->form->updatedProductId($productId);
    $this->productSearch = $productName;
    /* $this->products = []; */
  }
  public function updated($name, $value)
  {
    /* if ($name == "form.product_id") {
      $this->form->updatedProductId($value);
    } */
    if ($name == "form.unit_id") {
      $this->form->updatedUnitId($value);
    }
  }
  public function openModal($modalName)
  {
    $this->resetValidation();
    $this->form->resetOnly();
    $this->dispatch('open-modal', $modalName);
  }
  public function closeModal($modalName)
  {
    $this->resetValidation();
    $this->form->resetOnly();
    $this->dispatch('close-modal', $modalName);
  }
  public function save()
  {
    try {
      if ($this->form->area_product) {
        $this->form->update();
      } else {
        $this->form->store();
      }
      $this->alert('success', 'Se creó/actualizó con éxito');
      $this->closeModal($this->modalCreateOrUpdate);
    } catch (\Exception $e) {
      $this->alert('error', $e->getMessage());
    }
  }
  public function edit($areaProductId)
  {
    $this->resetValidation();
    $this->form->setAreaProduct($areaProductId);
    $this->dispatch('open-modal', $this->modalCreateOrUpdate);
  }
  public function delete($id)
  {
    $this->form->delete($id);
    $this->alert('success', 'Producto eliminado con éxito');
  }

  public function render()
  {
    $areaProducts = AreaProduct::where('area_id', $this->form->area->id)
      ->whereHas('product', function ($query) {
        $query->where('name', 'like', '%' . $this->search . '%');
      })
      ->paginate($this->perPage);

    $products = $this->form->products;

    return view('livewire.admin.area.area-product-page', [
      'areaProducts' => $areaProducts,
      'products' => $products,
      'units' => $this->form->units,
      'searchResults' => $this->searchProducts(),
    ]);
  }
}
