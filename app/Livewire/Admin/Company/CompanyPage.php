<?php

namespace App\Livewire\Admin\Company;

use App\Livewire\Forms\CompanyForm;
use App\Models\Company;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyPage extends Component
{
  use WithPagination;
  use LivewireAlert;

  public $search = '';
  public $perPage;
  public CompanyForm $form;
  public $viewMode;
  public $modalCreateOrUpdate = 'modal-create-or-update';

  public function mount()
  {
    $this->perPage = Cache::get('company_per_page', 10);
    $this->viewMode = Cache::get('company_view_mode', 'table');
  }

  public function updating($name, $value)
  {
    if ($name == "search") {
      $this->setPage(1);
    }
  }

  public function updatingPerPage($value)
  {
    Cache::put('company_per_page', $value);
    $this->resetPage();
  }

  public function updatedViewMode($value)
  {
    Cache::put('company_view_mode', $value);
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

  public function alertDelete($companyId)
  {
    $this->confirm('¿Está seguro de eliminar el elemento?', [
      'onConfirmed' => 'delete',
      'confirmButtonText' => 'Aceptar',
      'cancelButtonText' => 'Cancelar',
      'data' => ['id' => $companyId]
    ]);
  }

  #[On('delete')]
  public function delete($data)
  {
    $this->form->setCompany(Company::findOrFail($data['id']));
    $this->form->delete();
    $this->alert('success', 'Compañía eliminada con éxito');
  }

  public function save()
  {
    if ($this->form->company) {
      $this->form->update();
    } else {
      $this->form->store();
    }
    $this->alert('success', 'Se creó/actualizó con éxito');
    $this->closeModal($this->modalCreateOrUpdate);
  }

  public function edit($companyId)
  {
    $this->resetValidation();
    $this->form->setCompany(Company::findOrFail($companyId));
    $this->dispatch('open-modal', $this->modalCreateOrUpdate);
  }

  public function toggleViewMode()
  {
    $this->viewMode = $this->viewMode === 'table' ? 'grid' : 'table';
    $this->updatedViewMode($this->viewMode);
  }

  public function render()
  {
    return view('livewire.admin.company.company-page', [
      'companies' => Company::where('name', 'like', '%' . $this->search . '%')
        ->paginate($this->perPage),
    ]);
  }
}
