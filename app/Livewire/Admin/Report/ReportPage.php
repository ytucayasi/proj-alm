<?php

namespace App\Livewire\Admin\Report;

use App\Models\Reservation;
use App\Models\Area;
use App\Models\Company;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReservationsExport;

class ReportPage extends Component
{
  use WithPagination;

  public $area_id;
  public $company_id;
  public $search;
  public $start_date;
  public $end_date;
  public $selectedReservations = [];
  public $selectAll = false;

  public $totalCost = 0;
  public $totalPack = 0;
  public $totalProducts = 0;
  public $totalPaid = 0;
  public $total_ganado = 0;

  /* Area Variables */
  public $totalCostArea = 0;
  public $totalProductsArea = 0;
  public $total_ganadoArea = 0;

  public function mount()
  {
    /* $this->start_date = Carbon::today()->format('Y-m-d\T00:00'); */
    $this->start_date = Carbon::createFromFormat('Y-m-d\TH:i', '1900-01-01T00:00');
    $this->end_date = Carbon::today()->format('Y-m-d\T23:59');
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  public function updated($property)
  {
    $this->validateDates();
    $this->resetPage();
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  public function updatedSelectAll($value)
  {
    if ($value) {
      // Obtén las reservaciones visibles en la página actual
      $query = Reservation::query();
      $this->applyFilters($query);
      $visibleReservations = $query->pluck('id')->toArray();

      $this->selectedReservations = $visibleReservations;
    } else {
      $this->selectedReservations = [];
    }
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  protected function validateDates()
  {
    if ($this->end_date < $this->start_date) {
      $this->end_date = Carbon::parse($this->start_date)->addDay()->format('Y-m-d\TH:i');
    }

    $this->start_date = Carbon::parse($this->start_date)->format('Y-m-d\TH:i');
    $this->end_date = Carbon::parse($this->end_date)->format('Y-m-d\TH:i');
  }
  public function reportG()
  {
    $this->redirectRoute('reports.chart', navigate: true);
  }
  public function setToday()
  {
    $this->start_date = Carbon::today()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::today()->format('Y-m-d\T23:59');
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  public function setWeek()
  {
    $this->start_date = Carbon::now()->startOfWeek()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::now()->endOfWeek()->format('Y-m-d\T23:59');
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  public function setThreeWeeks()
  {
    $this->start_date = Carbon::now()->subWeeks(3)->startOfWeek()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::now()->endOfWeek()->format('Y-m-d\T23:59');
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  public function setPreviousYears()
  {
    $this->start_date = Carbon::createFromFormat('Y-m-d\TH:i', '1900-01-01T00:00'); // Fecha de inicio arbitrariamente antigua
    $this->end_date = Carbon::now()->format('Y-m-d\T23:59');
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  public function setMonth()
  {
    $this->start_date = Carbon::now()->startOfMonth()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::now()->endOfMonth()->format('Y-m-d\T23:59');
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  public function setThreeMonths()
  {
    $this->start_date = Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::now()->endOfMonth()->format('Y-m-d\T23:59');
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  public function clearFilters()
  {
    $this->reset(['area_id', 'company_id', 'search']);
    $this->start_date = Carbon::createFromFormat('Y-m-d\TH:i', '1900-01-01T00:00');
    $this->end_date = Carbon::today()->format('Y-m-d\T23:59');
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  private function applyFilters($query)
  {
    return $query->when($this->area_id, function ($query) {
      $query->whereHas('inventories', function ($query) {
        $query->where('type_area', $this->area_id);
      });
    })
      ->when($this->company_id, function ($query) {
        $query->whereHas('reservationCompanies', function ($query) {
          $query->where('company_id', $this->company_id);
        });
      })
      ->when($this->search, function ($query) {
        $query->whereHas('inventories.product', function ($query) {
          $query->where('name', 'like', "%{$this->search}%");
        });
      })
      ->whereBetween('execution_date', [$this->start_date, $this->end_date]);
  }
  public function calculateTotalsByArea()
  {
    if (!$this->area_id) {
      $this->totalCostArea = 0;
      $this->totalProductsArea = 0;
      $this->total_ganadoArea = 0;
      return;
    }

    $query = Reservation::query()
      ->join('inventories', 'reservations.id', '=', 'inventories.reservation_id')
      ->where('inventories.type_area', $this->area_id);

    if (!empty($this->selectedReservations)) {
      $query->whereIn('reservations.id', $this->selectedReservations);
    } else {
      $this->applyFilters($query);
    }

    // Cálculo del total de costo
    $this->totalCostArea = $query->selectRaw('SUM(inventories.quantity * inventories.unit_price) AS total_cost')
      ->first()->total_cost ?? 0;

    // Cálculo del total de productos
    $this->totalProductsArea = $query->selectRaw('COUNT(DISTINCT inventories.product_id) AS total_products')
      ->first()->total_products ?? 0;
    $this->total_ganadoArea = $this->totalCost - $this->totalCostArea;
  }

  public function updatedAreaId($value)
  {
    $this->calculateTotalsByArea();
  }
  public function calculateTotals()
  {
    if (!empty($this->selectedReservations)) {
      $query = Reservation::query()->whereIn('id', $this->selectedReservations);
    } else {
      $query = Reservation::query();
      $this->applyFilters($query);
    }

    $this->totalCost = $query->sum('total_cost');
    $this->totalPack = $query->sum('people_count');
    $this->totalProducts = $query->sum('total_products');
    /* $query->join('inventories', 'reservations.id', '=', 'inventories.reservation_id')
                              ->selectRaw('COUNT(DISTINCT inventories.product_id) as total_products')
                              ->first()->total_products; */
    $this->totalPaid = $query->where('payment_status', 1)->sum('total_pack');
    $this->total_ganado = $this->totalPaid - $this->totalCost;
  }

  public function updatedSelectedReservations()
  {
    $this->calculateTotals();
    $this->calculateTotalsByArea();
  }

  public function export()
  {
    $query = Reservation::query();
    $this->applyFilters($query);

    if (!empty($this->selectedReservations)) {
      $query->whereIn('id', $this->selectedReservations);
    }

    $reservations = $query->get();

    $totals = [
      'totalCost' => $this->totalCost,
      'totalPack' => $this->totalPack,
      'totalProducts' => $this->totalProducts,
      'totalPaid' => $this->totalPaid,
      'totalGanado' => $this->total_ganado,
    ];

    return Excel::download(new ReservationsExport($reservations, $totals), 'reservations.xlsx');
  }

  public function render()
  {
    $areas = Area::all();
    $companies = Company::all();

    $query = Reservation::query();
    $this->applyFilters($query);

    $reservations = $query->get();

    return view('livewire.admin.report.report-page', [
      'reservations' => $reservations,
      'areas' => $areas,
      'companies' => $companies,
      'totalCost' => $this->totalCost,
      'totalPack' => $this->totalPack,
      'totalProducts' => $this->totalProducts,
      'totalPaid' => $this->totalPaid,
      'totalGanado' => $this->total_ganado,
    ]);
  }
}
