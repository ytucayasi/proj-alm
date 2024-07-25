<?php

namespace App\Exports;

use App\Models\Reservation;
use App\Models\Inventory;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReservationInventoriesExport implements WithMultipleSheets
{
  protected $reservation;

  public function __construct($reservationId)
  {
    $this->reservation = Reservation::findOrFail($reservationId);
  }

  public function sheets(): array
  {
    $sheets = [];

    $inventories = Inventory::where('reservation_id', $this->reservation->id)->get();
    $inventoriesByArea = [];

    foreach ($inventories as $inventory) {
      $inventoriesByArea[$inventory->type_area][] = $inventory;
    }

    foreach ($inventoriesByArea as $area => $inventories) {
      $sheets[] = new AreaSheet($area, $inventories);
    }

    $sheets[] = new SummarySheet($this->reservation);

    return $sheets;
  }
}

class AreaSheet implements FromCollection, WithTitle, WithHeadings, WithMapping
{
  protected $area;
  protected $inventories;

  public function __construct($area, $inventories)
  {
    $this->area = $area;
    $this->inventories = $inventories;
  }

  public function collection()
  {
    return collect($this->inventories);
  }

  public function title(): string
  {
    return 'Área ' . \App\Models\Area::find($this->area)->name;
  }

  public function headings(): array
  {
    return [
      'Producto',
      'Unidad',
      'Tipo de Movimiento',
      'Cantidad',
      'Precio Unitario',
    ];
  }

  public function map($inventory): array
  {
    return [
      $inventory->product->name,
      $inventory->unit->name,
      $inventory->movement_type == 1 ? 'Entrada' : 'Salida',
      $inventory->quantity,
      $inventory->unit_price,
    ];
  }
}

class SummarySheet implements FromCollection, WithTitle, WithHeadings
{
  protected $reservation;

  public function __construct($reservation)
  {
    $this->reservation = $reservation;
  }

  public function collection()
  {
    $inventories = Inventory::where('reservation_id', $this->reservation->id)->get();
    $inventoriesByArea = [];

    foreach ($inventories as $inventory) {
      $inventoriesByArea[$inventory->type_area][] = $inventory;
    }

    $summary = [];

    foreach ($inventoriesByArea as $area => $inventories) {
      $totalQuantity = array_sum(array_column($inventories, 'quantity'));
      $totalCost = array_sum(array_map(function ($inventory) {
        return $inventory->quantity * $inventory->unit_price;
      }, $inventories));

      $summary[] = [
        'Área' => \App\Models\Area::find($area)->name,
        'Cantidad Total' => $totalQuantity,
        'Costo Total' => $totalCost,
      ];
    }

    $totalQuantity = array_sum(array_column($summary, 'Cantidad Total'));
    $totalCost = array_sum(array_column($summary, 'Costo Total'));

    $summary[] = [
      'Área' => 'Total General',
      'Cantidad Total' => $totalQuantity,
      'Costo Total' => $totalCost,
    ];

    return collect($summary);
  }

  public function title(): string
  {
    return 'Resumen';
  }

  public function headings(): array
  {
    return [
      'Área',
      'Cantidad Total',
      'Costo Total',
    ];
  }
}
