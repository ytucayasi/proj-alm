<?php

namespace App\Livewire\Admin\Reservation;

use App\Livewire\Forms\ReservationForm;
use App\Models\Reservation;
use Illuminate\Support\Facades\Cache;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationPage extends Component
{
  use WithPagination;
  use LivewireAlert;

  public $search = '';
  public $perPage;
  public ReservationForm $form;
  public $viewMode;
  public $modalCreateOrUpdate = 'modal-create-or-update';

  public function mount()
  {
    $this->perPage = Cache::get('reservation_per_page', 10);
    $this->viewMode = Cache::get('reservation_view_mode', 'table');
  }

  public function updatingPerPage($value)
  {
    Cache::put('reservation_per_page', $value);
    $this->resetPage();
  }

  public function updatedViewMode($value)
  {
    Cache::put('reservation_view_mode', $value);
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

  public function alertDelete($reservationId)
  {
    $this->confirm('¿Está seguro de eliminar el elemento?', [
      'onConfirmed' => 'delete',
      'confirmButtonText' => 'Aceptar',
      'cancelButtonText' => 'Cancelar',
      'data' => ['id' => $reservationId]
    ]);
  }

  public function view($reservationId)
  {
    $this->redirectRoute('reservations.show', ['reservationId' => $reservationId], navigate: true);
  }

  #[On('delete')]
  public function delete($data)
  {
    $this->form->setReservation(Reservation::findOrFail($data['id']));
    $this->form->delete();
    $this->alert('success', 'Reserva eliminada con éxito');
  }

  public function save()
  {
    if ($this->form->reservation) {
      $this->form->update();
    } else {
      $this->form->store();
    }
    $this->alert('success', 'Se creó/actualizó con éxito');
    $this->closeModal($this->modalCreateOrUpdate);
  }

  public function edit($reservationId)
  {
    $this->resetValidation();
    $this->form->setReservation(Reservation::findOrFail($reservationId));
    $this->dispatch('open-modal', $this->modalCreateOrUpdate);
  }

  public function toggleViewMode()
  {
    $this->viewMode = $this->viewMode === 'table' ? 'grid' : 'table';
    $this->updatedViewMode($this->viewMode);
  }

  public function render()
  {
    return view('livewire.admin.reservation.reservation-page', [
      'reservations' => Reservation::where('company_name', 'like', '%' . $this->search . '%')
        ->paginate($this->perPage),
    ]);
  }
}