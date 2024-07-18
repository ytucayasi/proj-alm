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
    $dataByDays = [];
    $current_day = $start_date->copy();
    while ($current_day->lte($end_date)) {
      $dataByDays[$current_day->format('Y-m-d')] = [
        'earnings' => 0,
        'total_cost' => 0,
        'total_pack' => 0
      ];
      $current_day->addDay();
    }

    $reservationsByDays = Reservation::whereBetween('execution_date', [$start_date, $end_date])
      ->select(
        DB::raw('DATE(execution_date) as date'),
        DB::raw('SUM(total_pack - total_cost) as earnings'),
        DB::raw('SUM(total_cost) as total_cost'),
        DB::raw('SUM(total_pack) as total_pack')
      )
      ->where('payment_status', 1)
      ->groupBy('date')
      ->get();

    foreach ($reservationsByDays as $reservation) {
      $dataByDays[$reservation->date] = [
        'earnings' => $reservation->earnings,
        'total_cost' => $reservation->total_cost,
        'total_pack' => $reservation->total_pack
      ];
    }

    return [
      'labels' => array_keys($dataByDays),
      'earnings' => array_column($dataByDays, 'earnings'),
      'total_cost' => array_column($dataByDays, 'total_cost'),
      'total_pack' => array_column($dataByDays, 'total_pack')
    ];
  }


}
