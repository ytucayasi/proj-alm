<?php

namespace App\Livewire\Admin\Reservation;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\Inventory;

class ReservationShow extends Component
{
  public $reservationId;
  public $reservation;
  public $inventories;

  public function mount($reservationId)
  {
    $this->reservationId = $reservationId;
    $this->reservation = Reservation::findOrFail($reservationId);
    $this->inventories = Inventory::where('reservation_id', $reservationId)->get();
  }

  public function render()
  {
    return view('livewire.admin.reservation.reservation-show', [
      'reservation' => $this->reservation,
      'inventories' => $this->inventories
    ]);
  }
}
