<?php

namespace App\Livewire\Admin\Category;

use App\Livewire\Forms\CategoryForm;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryPage extends Component
{
  use WithPagination;
  use LivewireAlert;

  public $search = '';
  public $perPage;
  public CategoryForm $form;
  public $viewMode;
  public $modalCreateOrUpdate = 'modal-create-or-update';
  public function mount()
  {
    $this->perPage = Cache::get('category_per_page', 10);
    $this->viewMode = Cache::get('category_view_mode', 'table');
  }
  public function updating($name, $value)
  {
    if ($name == "search") {
      $this->setPage(1);
    }
  }
  public function updatingPerPage($value)
  {
    Cache::put('category_per_page', $value);
    $this->resetPage();
  }
  public function updatedViewMode($value)
  {
    Cache::put('category_view_mode', $value);
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
  public function alertDelete($categoryId)
  {
    $this->confirm('¿Está seguro de eliminar el elemento?', [
      'onConfirmed' => 'delete',
      'confirmButtonText' => 'Aceptar',
      'cancelButtonText' => 'Cancelar',
      'data' => ['id' => $categoryId]
    ]);
  }
  #[On('delete')]
  public function delete($data)
  {
    $this->form->setCategory(Category::findOrFail($data['id']));
    $this->form->delete();
    $this->alert('success', 'Categoría eliminada con éxito');
  }
  public function save()
  {
    if ($this->form->category) {
      $this->form->update();
    } else {
      $this->form->store();
    }
    $this->alert('success', 'Se creó/actualizó con éxito');
    $this->closeModal($this->modalCreateOrUpdate);
  }
  public function edit($categoryId)
  {
    $this->resetValidation();
    $this->form->setCategory(Category::findOrFail($categoryId));
    $this->dispatch('open-modal', $this->modalCreateOrUpdate);
  }
  public function toggleViewMode()
  {
    $this->viewMode = $this->viewMode === 'table' ? 'grid' : 'table';
    $this->updatedViewMode($this->viewMode);
  }
  public function render()
  {
    return view('livewire.admin.category.category-page', [
      'categories' => Category::where('name', 'like', '%' . $this->search . '%')
        ->paginate($this->perPage),
    ]);
  }
}