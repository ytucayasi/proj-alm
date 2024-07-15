<?php

namespace App\Livewire\Admin\Reservation;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\Inventory;

class ReservationShow extends Component
{
  public $reservationId;
  public $reservation;
  public $inventoriesByArea = [];

  public function mount($reservationId)
  {
    $this->reservationId = $reservationId;
    $this->reservation = Reservation::findOrFail($reservationId);
    $this->loadInventoriesByArea();
  }

  public function loadInventoriesByArea()
  {
    $inventories = Inventory::where('reservation_id', $this->reservationId)->get();
    foreach ($inventories as $inventory) {
      $this->inventoriesByArea[$inventory->type_area][] = $inventory;
    }
  }

  public function render()
  {
    return view('livewire.admin.reservation.reservation-show', [
      'reservation' => $this->reservation,
      'inventoriesByArea' => $this->inventoriesByArea
    ]);
  }
}
