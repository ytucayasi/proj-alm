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
  public function mount($areaId)
  {
    $this->form->setArea($areaId);
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
    if ($this->form->area_product) {
      $this->form->update();
    } else {
      $this->form->store();
    }
    $this->alert('success', 'Se creó/actualizó con éxito');
    $this->closeModal($this->modalCreateOrUpdate);
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

    $products = Product::all();
    $units = Unit::all();

    return view('livewire.admin.area.area-product-page', [
      'areaProducts' => $areaProducts,
      'products' => $products,
      'units' => $units,
    ]);
  }
}
