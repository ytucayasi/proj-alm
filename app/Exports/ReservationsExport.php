<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReservationsExport implements FromCollection, WithHeadings, WithStyles
{
  protected $reservations;
  protected $totals;

  public function __construct($reservations, $totals)
  {
    $this->reservations = $reservations;
    $this->totals = $totals;
  }

  public function collection()
  {
    $data = $this->reservations->map(function ($reservation) {
      return [
        'ID' => $reservation->id,
        'Company Name' => implode(', ', $reservation->companies->pluck('company.name')->toArray()),
        'Status' => $this->getStatus($reservation->status),
        'Payment Status' => $this->getPaymentStatus($reservation->payment_status),
        'Order Date' => $reservation->order_date->format('Y/m/d H:i'),
        'Execution Date' => $reservation->execution_date ? $reservation->execution_date->format('Y/m/d H:i') : '',
        'Total Cost' => $reservation->total_cost,
        'People Count' => $reservation->people_count,
        'Total Pack' => $reservation->total_pack,
        'Total Products' => $reservation->total_products,
      ];
    });

    // Add totals row
    $data->push([
      'ID' => '',
      'Company Name' => '',
      'Status' => '',
      'Payment Status' => 'Totales:',
      'Order Date' => '',
      'Execution Date' => '',
      'Total Cost' => $this->totals['totalCost'],
      'People Count' => $this->totals['totalPack'],
      'Total Pack' => $this->totals['totalPaid'],
      'Total Products' => $this->totals['totalProducts'],
    ]);

    // Add total ganado row
    $data->push([
      'ID' => '',
      'Company Name' => '',
      'Status' => '',
      'Payment Status' => 'Total Ganado:',
      'Order Date' => '',
      'Execution Date' => '',
      'Total Cost' => '',
      'People Count' => '',
      'Total Pack' => '',
      'Total Products' => $this->totals['totalGanado'],
    ]);

    return $data;
  }

  public function headings(): array
  {
    return [
      'ID',
      'Empresas',
      'Estado',
      'Estado de Pago',
      'Fecha de Órden',
      'Fecha de Ejecución',
      'Costo Total',
      'Total Pack',
      'Total Pagado',
      'Total Productos',
    ];
  }

  protected function getStatus($status)
  {
    switch ($status) {
      case 1:
        return 'Realizado';
      case 2:
        return 'En Ejecución';
      case 3:
        return 'Pendiente';
      case 4:
        return 'Pospuesto';
      case 5:
        return 'Cancelado';
      default:
        return '';
    }
  }

  protected function getPaymentStatus($payment_status)
  {
    return $payment_status == 1 ? 'Pagado' : 'Pendiente de Pago';
  }

  public function styles(Worksheet $sheet)
  {
    $lastRow = $this->reservations->count() + 2;
    return [
      "A$lastRow:K$lastRow" => [
        'font' => ['bold' => true],
        'fill' => [
          'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
          'color' => ['argb' => 'FFFF00']
        ]
      ],
      "A" . ($lastRow + 1) . ":K" . ($lastRow + 1) => [
        'font' => ['bold' => true],
        'fill' => [
          'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
          'color' => ['argb' => 'FF00FF']
        ]
      ],
    ];
  }
}
