<?php

namespace App\Livewire\Admin\Reservation;

use App\Livewire\Forms\ReservationForm;
use App\Models\Product;
use Livewire\Component;

class ReservationCU extends Component
{
  public $reservationId = null;
  public ReservationForm $form;

  public function mount($reservationId = null)
  {
    if ($reservationId) {
      $this->reservationId = $reservationId;
    }
  }
  public function showVariations($productId)
  {
    $this->form->setProduct($productId);
  }
  public function selectVariation($variationId, $action)
  {
    $this->form->setVariation($variationId, $action);
  }
  public function incrementQuantity($variationId)
  {
    $this->form->incrementQuantityVariation($variationId);
  }
  public function decrementQuantity($variationId)
  {
    $this->form->decrementQuantityVariation($variationId);
  }
  public function render()
  {
    return view('livewire.admin.reservation.reservation-c-u', [
      "products" => Product::where("state", 1)->get(),
      "variations" => $this->form->variations,
      "selectedProducts" => $this->form->selectedProducts
    ]);
  }
}
