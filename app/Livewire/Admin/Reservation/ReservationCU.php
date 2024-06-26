<?php

namespace App\Livewire\Admin\Reservation;

use App\Livewire\Forms\ReservationForm;
use App\Models\Area;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationCU extends Component
{
  use WithPagination;
  use LivewireAlert;
  public ReservationForm $form;

  public function mount($reservationId = null)
  {
    $this->form->init($reservationId);
    $this->resetPage();
    if ($reservationId) {
      try {
        $this->form->setReservation($reservationId);
      } catch (\Exception $e) {
        $this->alert('error', $e->getMessage());
      }
    }
  }

  public function showVariations($productId)
  {
    $this->form->setProduct($productId);
  }

  public function updating($name, $value)
  {
    if ($name == "form.searchS") {
      $this->setPage(1);
    }
  }

  public function selectVariation($variationId, $action)
  {
    $this->form->setVariation($variationId, $action);
    if ($action == "delete") {
      $this->resetPage();
    }
  }

  public function incrementQuantity($variationId)
  {
    $this->form->incrementQuantityVariation($variationId);
  }

  public function decrementQuantity($variationId)
  {
    $this->form->decrementQuantityVariation($variationId);
  }

  public function selectArea($areaId)
  {
    $this->form->setArea($areaId);
  }

  public function clearErrorStock()
  {
    $this->form->productsExceedingStock = [];
  }

  public function save()
  {
    try {
      if ($this->form->reservation) {
        \Log::info('Actualizando reserva: ', ['reservation' => $this->form->reservation]);
        $this->form->update();
      } else {
        \Log::info('Creando nueva reserva');
        $this->form->store();
      }
      $this->redirectRoute('reservations.index', navigate: true);
    } catch (\Exception $e) {
      \Log::error('Error al guardar la reserva: ' . $e->getMessage());
      $this->alert('error', $e->getMessage());
    }
  }

  private function filterAndPaginate($items, $perPage = 10, $page = null, $options = [])
  {
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);

    $filteredItems = $items->filter(function ($product) {
      return str_contains(strtolower($product['product_name'] ?? ''), strtolower($this->form->searchS));
    });

    return new LengthAwarePaginator(
      $filteredItems->forPage($page, $perPage),
      $filteredItems->count(),
      $perPage,
      $page,
      $options
    );
  }

  public function render()
  {
    $products = Product::where('state', 1)
      ->where('name', 'like', '%' . $this->form->searchP . '%')
      ->take(10)
      ->get();

    $filteredSelectedProducts = $this->filterAndPaginate($this->form->selectedProducts ?? collect());

    return view('livewire.admin.reservation.reservation-c-u', [
      'products' => $products,
      'areas' => Area::where('state', 1)->get(),
      'variations' => $this->form->variations ?? collect(),
      'selectedProducts' => $filteredSelectedProducts
    ]);
  }
}
