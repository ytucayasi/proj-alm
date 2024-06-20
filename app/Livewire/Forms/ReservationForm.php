<?php

namespace App\Livewire\Forms;

use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Form;
use App\Models\Reservation;

class ReservationForm extends Form
{
  public ?Reservation $reservation = null;
  #[Locked]
  public $id = null;
  public $company_name = '';
  public $description = '';
  public $status = 3;
  public $payment_status = 2;
  public $type = 1;
  public $order_date = null;
  public $execution_date = null;
  public $total_cost = 0.00;
  public $people_count = 0;
  public $total_products = 0;

  public function rules()
  {
    return [
      'company_name' => 'required|string|max:150',
      'description' => 'nullable|string',
      'status' => 'required|integer|in:1,2,3,4,5',
      'payment_status' => 'required|integer|in:1,2',
      'type' => 'required|integer',
      'order_date' => 'required|date',
      'execution_date' => 'nullable|date',
      'total_cost' => 'required|numeric|min:0',
      'people_count' => 'required|integer|min:1',
      'total_products' => 'required|integer|min:0',
    ];
  }

  public function setReservation(Reservation $reservation)
  {
    $this->reservation = $reservation;
    $this->id = $reservation->id;
    $this->company_name = $reservation->company_name;
    $this->description = $reservation->description;
    $this->status = $reservation->status;
    $this->payment_status = $reservation->payment_status;
    $this->type = $reservation->type;
    $this->order_date = $reservation->order_date->format('Y-m-d\TH:i');
    $this->execution_date = optional($reservation->execution_date)->format('Y-m-d\TH:i');
    $this->total_cost = $reservation->total_cost;
    $this->people_count = $reservation->people_count;
    $this->total_products = $reservation->total_products;
  }

  public function store()
  {
    $this->validate();
    Reservation::create($this->all());
    $this->reset();
  }

  public function update()
  {
    $this->validate();
    $this->reservation->update($this->all());
    $this->reset();
  }

  public function delete()
  {
    $this->reservation->delete();
  }
}
