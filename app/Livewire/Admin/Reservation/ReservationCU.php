<?php

namespace App\Livewire\Admin\Reservation;

use App\Livewire\Forms\ReservationForm;
use App\Models\Area;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationCU extends Component
{
  use WithPagination;
  public $reservationId = null;
  public ReservationForm $form;

  public function mount($reservationId = null)
  {
    $this->resetPage();
    if ($reservationId) {
      $this->reservationId = $reservationId;
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
  private function filterAndPaginate($items, $perPage = 10, $page = null, $options = [])
  {
    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
    $items = $items instanceof Collection ? $items : Collection::make($items);
    $filteredItems = $items->filter(function ($product) {
      return str_contains(strtolower($product['product_name']), strtolower($this->form->searchS));
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
    $filteredSelectedProducts = $this->filterAndPaginate($this->form->selectedProducts, 1);
    return view('livewire.admin.reservation.reservation-c-u', [
      'products' => $products,
      'areas' => Area::where('state', 1)->get(),
      'variations' => $this->form->variations,
      'selectedProducts' => $filteredSelectedProducts
    ]);
  }
}
