<?php

namespace App\Livewire\Admin\Report;

use Livewire\Component;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReportG extends Component
{
  public function render()
  {
    // Configurar fechas de inicio y fin del mes actual
    $today = now();
    $start_date = $today->copy()->startOfMonth();
    $end_date = $today->copy()->endOfMonth();

    // Obtener los datos de las reservaciones por días del mes
    $dataByDays = $this->getReservationsByDays($start_date, $end_date);

    // Obtener los datos de las reservaciones por áreas
    $dataByArea = $this->getReservationsByArea($start_date);

    // Obtener las ganancias diarias
    $dailyEarnings = $this->getDailyEarnings($start_date, $end_date);

    return view('livewire.admin.report.report-g', [
      'dataByDays' => $dataByDays,
      'dataByArea' => $dataByArea,
      'dailyEarnings' => $dailyEarnings,
    ]);
  }

  private function getReservationsByDays($start_date, $end_date)
  {
    // Inicializar el arreglo para los datos por días del mes
    $dataByDays = [];
    $current_day = $start_date->copy();
    while ($current_day->lte($end_date)) {
      $dataByDays[$current_day->format('Y-m-d')] = 0;
      $current_day->addDay();
    }

    // Obtener las reservaciones por días del mes
    $reservationsByDays = Reservation::whereBetween('execution_date', [$start_date, $end_date])
      ->select(DB::raw('DATE(execution_date) as date'), DB::raw('count(*) as count'))
      ->groupBy('date')
      ->get();

    // Llenar el arreglo con los datos de las reservaciones por días del mes
    foreach ($reservationsByDays as $reservation) {
      $dataByDays[$reservation->date] = $reservation->count;
    }

    return [
      'labels' => array_keys($dataByDays),
      'data' => array_values($dataByDays),
    ];
  }

  private function getReservationsByArea($start_date)
  {
    // Obtener las reservaciones por áreas
    $reservationsByArea = Reservation::join('inventories', 'reservations.id', '=', 'inventories.reservation_id')
      ->join('areas', 'inventories.type_area', '=', 'areas.id')
      ->select('areas.name as area_name', DB::raw('count(distinct reservations.id) as count'))
      ->where('reservations.execution_date', '>=', $start_date)
      ->groupBy('areas.name')
      ->get();

    return [
      'labels' => $reservationsByArea->pluck('area_name')->toArray(),
      'data' => $reservationsByArea->pluck('count')->toArray(),
    ];
  }

  private function getDailyEarnings($start_date, $end_date)
  {
    // Inicializar el arreglo para las ganancias por días del mes
    $earningsByDays = [];
    $current_day = $start_date->copy();
    while ($current_day->lte($end_date)) {
      $earningsByDays[$current_day->format('Y-m-d')] = 0;
      $current_day->addDay();
    }

    // Obtener las ganancias diarias
    $earningsByDay = Reservation::whereBetween('execution_date', [$start_date, $end_date])
      ->where('payment_status', 1)
      ->select(DB::raw('DATE(execution_date) as date'), DB::raw('SUM(total_pack - total_cost) as earnings'))
      ->groupBy('date')
      ->get();

    // Llenar el arreglo con los datos de las ganancias por días del mes
    foreach ($earningsByDay as $earning) {
      $earningsByDays[$earning->date] = $earning->earnings;
    }

    return [
      'labels' => array_keys($earningsByDays),
      'data' => array_values($earningsByDays),
    ];
  }
}
