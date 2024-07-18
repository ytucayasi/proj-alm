<?php

namespace App\Livewire\Admin\Report;

use Livewire\Component;
use App\Models\Reservation;
use App\Models\Inventory;
use App\Models\Area; // Asegúrate de importar el modelo de Área
use Illuminate\Support\Facades\DB;

class ReportG extends Component
{
  public function render()
  {
    // Obtener los datos actuales del gráfico (por días del mes)
    $today = now();
    $start_date = $today->copy()->startOfMonth(); // Primer día del mes actual
    $end_date = $today->copy()->endOfMonth();     // Último día del mes actual

    // Inicializar el arreglo para los datos por días del mes
    $dataByDays = [];
    $current_day = $start_date->copy();
    while ($current_day->lte($end_date)) {
      $dataByDays[$current_day->format('Y-m-d')] = 0; // Inicializar con 0 reservaciones
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

    // Obtener las reservaciones por áreas
    $reservationsByArea = Inventory::join('reservations', 'inventories.reservation_id', '=', 'reservations.id')
      ->join('areas', 'inventories.type_area', '=', 'areas.id') // Unir con la tabla de áreas para obtener los nombres
      ->select('areas.name as area_name', DB::raw('count(*) as count'))
      ->where('reservations.execution_date', '>=', now()->startOfMonth()) // Considerar reservaciones del mes actual
      ->groupBy('inventories.type_area')
      ->get();

    // Preparar los datos para el gráfico por áreas
    $labelsByArea = $reservationsByArea->pluck('area_name')->toArray();
    $countsByArea = $reservationsByArea->pluck('count')->toArray();

    // Configurar los datos para los gráficos
    $chartDataByDays = [
      'labels' => array_keys($dataByDays),
      'data' => array_values($dataByDays),
    ];

    $chartDataByArea = [
      'labels' => $labelsByArea,
      'data' => $countsByArea,
    ];

    return view('livewire.admin.report.report-g', [
      'dataByDays' => $chartDataByDays,
      'dataByArea' => $chartDataByArea,
    ]);
  }
}
