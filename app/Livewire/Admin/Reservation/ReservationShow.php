<?php

namespace App\Livewire\Admin\Reservation;

use App\Exports\ReservationInventoriesExport;
use Livewire\Component;
use App\Models\Reservation;
use App\Models\Inventory;
use Maatwebsite\Excel\Facades\Excel;

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

  public function export()
  {
    return Excel::download(new ReservationInventoriesExport($this->reservationId), 'reservation_inventories.xlsx');
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
