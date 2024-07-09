<?php

namespace App\Livewire\Admin\Reservation;

use App\Livewire\Forms\ReservationForm;
use App\Models\Area;
use App\Models\Company;
use App\Models\Product;
use App\Models\ReservationCompany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ReservationCU extends Component
{
  use LivewireAlert;
  public ReservationForm $form;
  public $companySearch = "";
  public $modalCreateCompany = 'modal-create-company';
  public $name = ""; // El nombre de la compañía
  public $ruc = "NAN";
  public $selectedCompanies = [];

  public function mount($reservationId = null)
  {
    $this->form->init($reservationId);
    if ($reservationId) {
      try {
        $this->form->setReservation($reservationId);
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
    $this->selectedCompanies[] = [
      'id' => $companyId,
      'name' => $companyName,
      'pack' => 0,
      'cost_pack' => 0,
      'total_pack' => 0,
    ];
    $this->companySearch = "";
  }

  public function recargar_precios()
  {
    $this->form->recargar_precios();
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
      // No need to set the page to 1 anymore
    }
  }

  public function updated($name, $value)
  {
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
      $this->selectCompany($company->id, $company->name);
      $this->closeModal($this->modalCreateCompany);
    } catch (\Exception $e) {
      $this->alert('error', $e->getMessage());
    }
  }

  public function selectVariation($variationId, $action, $areaId)
  {
    try {
      $this->form->setVariation($variationId, $action, $areaId);
      if ($action == "delete") {
        // No need to reset the page anymore
      }
    } catch (\Exception $e) {
      \Log::error('Error de edición de tabla: ' . $e->getMessage());
      $this->alert('error', $e->getMessage());
    }
  }

  public function incrementQuantity($variationId, $areaId)
  {
    $this->form->incrementQuantityVariation($variationId, $areaId);
  }

  public function decrementQuantity($variationId, $areaId)
  {
    $this->form->decrementQuantityVariation($variationId, $areaId);
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

  public function render()
  {
    $products = Product::where('state', 1)
      ->where('name', 'like', '%' . $this->form->searchP . '%')
      ->where('product_type', 1)
      ->whereHas('variations')
      ->get(); // No pagination or limit

    return view('livewire.admin.reservation.reservation-c-u', [
      'products' => $products,
      'areas' => Area::where('state', 1)->get(),
      'variations' => $this->form->variations ?? collect(),
      'selectedProducts' => $this->form->selectedProducts->flatten(1) ?? collect(), // No pagination
      'searchResults' => $this->searchCompanies(),
    ]);
  }
}
