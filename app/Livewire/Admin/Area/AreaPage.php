<?php

namespace App\Livewire\Admin\Area;

use App\Livewire\Forms\AreaForm;
use App\Models\Area;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class AreaPage extends Component
{
  use WithPagination;
  use LivewireAlert;

  public $search = '';
  public $perPage;
  public AreaForm $form;
  public $viewMode;
  public $modalCreateOrUpdate = 'modal-create-or-update';

  public function mount()
  {
    $this->perPage = Cache::get('area_per_page', 10);
    $this->viewMode = Cache::get('area_view_mode', 'table');
  }

  public function updatingPerPage($value)
  {
    Cache::put('area_per_page', $value);
    $this->resetPage();
  }

  public function updatedViewMode($value)
  {
    Cache::put('area_view_mode', $value);
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

  public function alertDelete($areaId)
  {
    $this->confirm('¿Está seguro de eliminar el elemento?', [
      'onConfirmed' => 'delete',
      'confirmButtonText' => 'Aceptar',
      'cancelButtonText' => 'Cancelar',
      'data' => ['id' => $areaId]
    ]);
  }

  #[On('delete')]
  public function delete($data)
  {
    $this->form->setArea(Area::findOrFail($data['id']));
    $this->form->delete();
    $this->alert('success', 'Área eliminada con éxito');
  }

  public function save()
  {
    if ($this->form->area) {
      $this->form->update();
    } else {
      $this->form->store();
    }
    $this->alert('success', 'Se creó/actualizó con éxito');
    $this->closeModal($this->modalCreateOrUpdate);
  }

  public function edit($areaId)
  {
    $this->resetValidation();
    $this->form->setArea(Area::findOrFail($areaId));
    $this->dispatch('open-modal', $this->modalCreateOrUpdate);
  }

  public function toggleViewMode()
  {
    $this->viewMode = $this->viewMode === 'table' ? 'grid' : 'table';
    $this->updatedViewMode($this->viewMode);
  }

  public function selectedProducts($areaId)
  {
    return redirect()->route('areas.products', ['areaId' => $areaId]);
  }

  public function render()
  {
    return view('livewire.admin.area.area-page', [
      'areas' => Area::where('name', 'like', '%' . $this->search . '%')
        ->paginate($this->perPage),
    ]);
  }
}
