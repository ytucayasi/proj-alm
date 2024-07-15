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

  public function mount()
  {
    $this->start_date = Carbon::today()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::today()->format('Y-m-d\T23:59');
    $this->calculateTotals();
  }

  public function updated($property)
  {
    $this->validateDates();
    $this->resetPage();
    $this->calculateTotals();
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
  }

  protected function validateDates()
  {
    if ($this->end_date < $this->start_date) {
      $this->end_date = Carbon::parse($this->start_date)->addDay()->format('Y-m-d\TH:i');
    }
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
  }

  public function setWeek()
  {
    $this->start_date = Carbon::now()->startOfWeek()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::now()->endOfWeek()->format('Y-m-d\T23:59');
    $this->calculateTotals();
  }

  public function setThreeWeeks()
  {
    $this->start_date = Carbon::now()->subWeeks(3)->startOfWeek()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::now()->endOfWeek()->format('Y-m-d\T23:59');
    $this->calculateTotals();
  }

  public function setMonth()
  {
    $this->start_date = Carbon::now()->startOfMonth()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::now()->endOfMonth()->format('Y-m-d\T23:59');
    $this->calculateTotals();
  }

  public function setThreeMonths()
  {
    $this->start_date = Carbon::now()->subMonths(3)->startOfMonth()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::now()->endOfMonth()->format('Y-m-d\T23:59');
    $this->calculateTotals();
  }

  public function clearFilters()
  {
    $this->reset(['area_id', 'company_id', 'search']);
    $this->start_date = Carbon::today()->format('Y-m-d\T00:00');
    $this->end_date = Carbon::today()->format('Y-m-d\T23:59');
    $this->calculateTotals();
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
      ->whereBetween('order_date', [$this->start_date, $this->end_date]);
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
    $this->totalPaid = $query->where('payment_status', 1)->sum('total_pack');
    $this->total_ganado = $this->totalPaid - $this->totalCost;
  }

  public function updatedSelectedReservations()
  {
    $this->calculateTotals();
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

    $reservations = $query->paginate(10);

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
