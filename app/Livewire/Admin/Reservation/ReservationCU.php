<?php

namespace App\Livewire\Admin\Reservation;

use App\Livewire\Forms\ReservationForm;
use App\Models\Area;
use App\Models\Company;
use App\Models\Product;
use App\Models\ReservationCompany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationCU extends Component
{
  use WithPagination;
  use LivewireAlert;
  public ReservationForm $form;
  public $companySearch = "";
  public $modalCreateCompany = 'modal-create-company';
  public $name = ""; // El nombre de la compania
  public $ruc = "NAN";
  public $selectedCompanies = [];
  public function mount($reservationId = null)
  {
    $this->form->init($reservationId);
    $this->resetPage();
    if ($reservationId) {
      try {
        $this->form->setReservation($reservationId);
        /* $this->companySearch = $this->form->company_name; */
        foreach ($this->form->reservation->companies as $company) {
          $this->selectedCompanies[] = [
            'id' => $company->company->id,
            'name' => $company->company->name,
            'pack' => $company->pack,
            'cost_pack' => $company->cost_pack,
            'total_pack' => $company->total_pack,
          ];
        }
      } catch (\Exception $e) {
        $this->alert('error', $e->getMessage());
      }
    }
  }
  public function openModal($modalName)
  {
    $this->resetValidation();
    $this->name = "";
    $this->dispatch('open-modal', $modalName);
  }
  public function closeModal($modalName)
  {
    $this->resetValidation();
    $this->name = "";
    $this->dispatch('close-modal', $modalName);
  }
  public function searchCompanies()
  {
    return Company::where('name', 'like', '%' . $this->companySearch . '%')->take(5)->get();
  }
  public function selectCompany($companyId, $companyName)
  {
    /*     if ($this->validateCompany($companyId)) {
          return;
        } else {
          $this->selectedCompanies[] = [
            'id' => $companyId,
            'name' => $companyName,
            'pack' => 0,
            'cost_pack' => 0,
            'total_pack' => 0,
          ];
          $this->companySearch = "";
        } */
    $this->selectedCompanies[] = [
      'id' => $companyId,
      'name' => $companyName,
      'pack' => 0,
      'cost_pack' => 0,
      'total_pack' => 0,
    ];
    $this->companySearch = "";
  }
  public function validateCompany($companyId)
  {
    foreach ($this->selectedCompanies as $company) {
      if ($company['id'] == $companyId) {
        return true;
      }
    }
    return false;
  }
  public function removeCompany($index)
  {
    array_splice($this->selectedCompanies, $index, 1);
    $this->updateFormTotals();
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
  public function updated($name, $value)
  {
    /* if ($name == "form.cost_pack" || $name == "form.people_count") {
      if ($value < 0) {
        $this->alert('error', 'Los valores no pueden ser negativos.');
        $this->form->total_pack = 0;  // Reiniciar a cero si el valor es negativo
        return;
      }
      $this->form->validateOnly($name, [
        'cost_pack' => 'required|numeric|min:0',
        'people_count' => 'required|integer|min:1',
      ]);
      $this->form->total_pack = $this->form->cost_pack * $this->form->people_count;
    } */

    // Detectar cambios en los campos pack y cost_pack dentro de selectedCompanies
    /* selectedCompanies.0.pack */
    if (strpos($name, 'selectedCompanies.') === 0) {
      $parts = explode('.', $name);
      if (isset($parts[1]) && isset($parts[2])) {
        $index = $parts[1];
        $field = $parts[2];

        if ($field === 'pack' || $field === 'cost_pack') {
          $pack = $this->selectedCompanies[$index]['pack'] ?? null;
          $costPack = $this->selectedCompanies[$index]['cost_pack'] ?? null;

          if (is_null($pack) || is_null($costPack) || $pack === '' || $costPack === '') {
            $this->alert('error', 'Por favor, ingrese un valor para Pack y Costo por Pack.');
            $this->selectedCompanies[$index]['total_pack'] = 0;
            $this->updateFormTotals();
            return;
          }
          // Calcular y actualizar total_pack
          $this->selectedCompanies[$index]['total_pack'] = $pack * $costPack;
        }
        $this->updateFormTotals();
      }
    }
  }
  protected function updateFormTotals()
  {
    $totalPack = 0;
    $totalCostPack = 0;
    $totalTotalPack = 0;
    foreach ($this->selectedCompanies as $company) {
      $pack = $company['pack'] ?? null;
      $costPack = $company['cost_pack'] ?? null;
      $totalPackVal = $company['total_pack'] ?? 0;
      if ($pack === null || $pack === '' || $costPack === null || $costPack === '') {
        $this->form->people_count = 0;
        $this->form->cost_pack = 0;
        $this->form->total_pack = 0;
        return;
      }
      $totalPack += $pack;
      $totalCostPack += $costPack;
      $totalTotalPack += $totalPackVal;
    }
    $this->form->people_count = $totalPack;
    $this->form->cost_pack = $totalCostPack;
    $this->form->total_pack = $totalTotalPack;
  }
  public function saveCompany()
  {
    try {
      $company = Company::create(
        $this->only(['name', 'ruc'])
      );
      $this->alert('success', 'Se creó la empresa');
      $this->resetValidation();
      /* $this->companySearch = $company->name; */
      /* $this->form->company_name = $company->name; */
      $this->selectCompany($company->id, $company->name);
      $this->closeModal($this->modalCreateCompany);
    } catch (\Exception $e) {
      $this->alert('error', $e->getMessage());
    }
  }
  public function selectVariation($variationId, $action)
  {
    try {
      $this->form->setVariation($variationId, $action);
      if ($action == "delete") {
        $this->resetPage();
      }
    } catch (\Exception $e) {
      \Log::error('Error de edición de tabla: ' . $e->getMessage());
      $this->alert('error', $e->getMessage());
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
  private function validateSelectedCompanies()
  {
    if (empty($this->selectedCompanies)) {
      $this->alert('error', 'Debe seleccionar al menos una empresa.');
      return false;
    }
    foreach ($this->selectedCompanies as $company) {
      if (empty($company['id']) || empty($company['name']) || empty($company['pack']) || empty($company['cost_pack']) || empty($company['total_pack'])) {
        $this->alert('error', 'Todos los campos de cada empresa seleccionada deben estar llenos.');
        return false;
      }
    }
    return true;
  }
  public function save()
  {
    if (!$this->validateSelectedCompanies()) {
      return;
    }
    try {
      if ($this->form->reservation) {
        \Log::info('Actualizando reserva: ', ['reservation' => $this->form->reservation]);
        $this->form->update();
        DB::table('reservation_companies')->where('reservation_id', $this->form->id)
          ->delete();
        $this->saveCompaniesSelected();
      } else {
        \Log::info('Creando nueva reserva');
        $this->form->store();
        $this->saveCompaniesSelected();
      }
      $this->redirectRoute('reservations.index', navigate: true);
    } catch (\Exception $e) {
      \Log::error('Error al guardar la reserva: ' . $e->getMessage());
      $this->alert('error', $e->getMessage());
    }
  }
  private function saveCompaniesSelected()
  {
    foreach ($this->selectedCompanies as $company) {
      DB::table('reservation_companies')->insert([
        'reservation_id' => $this->form->id,
        'company_id' => $company['id'],
        'pack' => $company['pack'],
        'cost_pack' => $company['cost_pack'],
        'total_pack' => $company['total_pack'],
      ]);
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
      ->where('product_type', 1)
      ->whereHas('variations') // Filtrar productos que tienen variaciones
      ->take(10)
      ->get();
    $filteredSelectedProducts = $this->filterAndPaginate($this->form->selectedProducts ?? collect());
    return view('livewire.admin.reservation.reservation-c-u', [
      'products' => $products,
      'areas' => Area::where('state', 1)->get(),
      'variations' => $this->form->variations ?? collect(),
      'selectedProducts' => $filteredSelectedProducts,
      'searchResults' => $this->searchCompanies(),
    ]);
  }
}
