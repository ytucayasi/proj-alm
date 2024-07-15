<?php

namespace App\Livewire\Admin\Report;

use Livewire\Component;
use App\Models\ReservationCompany;
use App\Models\Inventory;
use Illuminate\Support\Facades\DB;

class ReportG extends Component
{
  public $viewType = 'chart'; // 'chart' or 'description'
  public $data = [];
  public $areaData = []; // Nueva propiedad para los datos de áreas

  public function mount()
  {
    $this->loadData();
    $this->loadAreaData(); // Cargar datos de áreas
  }

  public function loadData()
  {
    $this->data = ReservationCompany::select('company_id', DB::raw('count(*) as total_reservations'))
      ->groupBy('company_id')
      ->with('company')
      ->get()
      ->map(function ($item) {
        return [
          'company_name' => $item->company->name,
          'total_reservations' => $item->total_reservations
        ];
      });

    $this->viewType = 'chart'; // Asegurarse de que el viewType sea 'chart' cuando se cargan los datos
    $this->dispatch('chart-show', data: $this->data);
  }

  public function loadAreaData()
  {
    $this->areaData = Inventory::select('type_area', DB::raw('count(DISTINCT reservation_id) as total_reservations'))
      ->groupBy('type_area')
      ->get()
      ->map(function ($item) {
        $areaName = DB::table('areas')->where('id', $item->type_area)->value('name');
        return [
          'area_name' => $areaName,
          'total_reservations' => $item->total_reservations
        ];
      });

    $this->dispatch('area-chart-show', ['data' => $this->areaData]);
  }

  public function toggleView()
  {
    $this->viewType = $this->viewType === 'chart' ? 'description' : 'chart';
    if ($this->viewType === 'chart') {
      $this->dispatch('chart-show', data: $this->data);
      $this->dispatch('area-chart-show', data: $this->areaData); // Asegurar la actualización del gráfico de áreas
    }
  }

  public function render()
  {
    return view('livewire.admin.report.report-g', [
      'viewType' => $this->viewType,
      'data' => $this->data,
      'areaData' => $this->areaData
    ]);
  }
}
