<?php

namespace App\Livewire\Admin\Unit;

use App\Livewire\Forms\UnitForm;
use App\Models\Unit;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class UnitPage extends Component
{
  use WithPagination;
  use LivewireAlert;

  public $search = '';
  public $perPage;
  public UnitForm $form;
  public $viewMode;
  public $modalCreateOrUpdate = 'modal-create-or-update';

  public function mount()
  {
    $this->perPage = Cache::get('unit_per_page', 10);
    $this->viewMode = Cache::get('unit_view_mode', 'table');
  }
  public function updating($name, $value)
  {
    if ($name == "search") {
      $this->setPage(1);
    }
  }
  public function updatingPerPage($value)
  {
    Cache::put('unit_per_page', $value);
    $this->resetPage();
  }

  public function updatedViewMode($value)
  {
    Cache::put('unit_view_mode', $value);
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

  public function alertDelete($unitId)
  {
    $this->confirm('¿Está seguro de eliminar el elemento?', [
      'onConfirmed' => 'delete',
      'confirmButtonText' => 'Aceptar',
      'cancelButtonText' => 'Cancelar',
      'data' => ['id' => $unitId]
    ]);
  }

  #[On('delete')]
  public function delete($data)
  {
    $this->form->setUnit(Unit::findOrFail($data['id']));
    $this->form->delete();
    $this->alert('success', 'Unidad eliminada con éxito');
  }

  public function save()
  {
    if ($this->form->unit) {
      $this->form->update();
    } else {
      $this->form->store();
    }
    $this->alert('success', 'Se creó/actualizó con éxito');
    $this->closeModal($this->modalCreateOrUpdate);
  }

  public function edit($unitId)
  {
    $this->resetValidation();
    $this->form->setUnit(Unit::findOrFail($unitId));
    $this->dispatch('open-modal', $this->modalCreateOrUpdate);
  }

  public function toggleViewMode()
  {
    $this->viewMode = $this->viewMode === 'table' ? 'grid' : 'table';
    $this->updatedViewMode($this->viewMode);
  }

  public function render()
  {
    return view('livewire.admin.unit.unit-page', [
      'units' => Unit::where('name', 'like', '%' . $this->search . '%')
        ->paginate($this->perPage),
    ]);
  }
}