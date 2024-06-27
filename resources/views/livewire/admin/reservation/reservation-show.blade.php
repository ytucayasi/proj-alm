{{-- Encabezado --}}
<x-slot name="header">
  <h2 class="text-xl font-semibold leading-tight text-gray-800">
    {{ __('Reservación #') . $reservation->id }}
  </h2>
</x-slot>

{{-- Contenido --}}
<div>
  <div class="mb-6">
    <h3 class="mb-4 text-lg font-bold text-gray-900">Detalles de la Reservación</h3>
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700"><strong>Nombre de la Empresa:</strong> {{ $reservation->company_name }}</p>
      </div>
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700">
          <strong>Descripción:</strong>
          {{ $reservation->description ? trim($reservation->description) : 'Sin Descripción' }}
        </p>
      </div>
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700"><strong>Estado de la Reservación:</strong>
          <span
            class="{{ $reservation->status == 1 ? 'bg-green-100 text-green-800' : '' }} {{ $reservation->status == 2 ? 'bg-blue-100 text-blue-800' : '' }} {{ $reservation->status == 3 ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $reservation->status == 5 ? 'bg-red-100 text-red-800' : '' }} {{ $reservation->status == 4 ? 'bg-purple-100 text-purple-800' : '' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
            @switch($reservation->status)
              @case(1)
                En Proceso
              @break

              @case(2)
                En Ejecución
              @break

              @case(3)
                Pendiente
              @break

              @case(4)
                Pospuesto
              @break

              @case(5)
                Cancelado
              @break
            @endswitch
          </span>
        </p>
      </div>
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700"><strong>Estado del Pago:</strong>
          <span
            class="{{ $reservation->payment_status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
            {{ $reservation->payment_status == 1 ? 'Pagado' : 'No Pagado' }}
          </span>
        </p>
      </div>
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700"><strong>Fecha del Pedido:</strong> <span
            class="text-sm">{{ \Carbon\Carbon::parse($reservation->order_date)->format('Y-m-d H:i') }}</span></p>
      </div>
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700"><strong>Fecha de Ejecución:</strong> <span
            class="text-sm">{{ \Carbon\Carbon::parse($reservation->execution_date)->format('Y-m-d H:i') }}</span></p>
      </div>
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700"><strong>Tipo:</strong>
          <span
            class="{{ $reservation->type == 1 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
            {{ $reservation->type == 1 ? 'Normal' : 'Otros' }}
          </span>
        </p>
      </div>
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700"><strong>Costo Total:</strong> S/. {{ $reservation->total_cost }}</p>
      </div>
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700"><strong>Cantidad de Personas:</strong> {{ $reservation->people_count }} personas</p>
      </div>
      <div class="rounded-lg bg-blue-50 p-4 shadow">
        <p class="text-gray-700"><strong>Total de Productos:</strong> {{ $reservation->total_products }} productos</p>
      </div>
    </div>
  </div>

  <div>
    <h3 class="mb-4 text-lg font-bold text-gray-900">Inventarios Registrados</h3>
    <div class="overflow-x-auto">
      <table class="min-w-full rounded-lg border border-gray-200 bg-white shadow-lg">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-700">
              Producto</th>
            <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-700">
              Unidad</th>
            <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-700">
              Tipo de Movimiento</th>
            <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-700">
              Cantidad</th>
            <th class="px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-700">
              Precio Unitario
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @foreach ($inventories as $inventory)
            <tr>
              <td class="whitespace-nowrap px-4 py-2 text-center">{{ $inventory->product->name }}</td>
              <td class="whitespace-nowrap px-4 py-2 text-center">{{ $inventory->unit->name }}</td>
              <td class="whitespace-nowrap px-4 py-2 text-center">
                {{ $inventory->movement_type == 1 ? 'Entrada' : 'Salida' }}</td>
              <td class="whitespace-nowrap px-4 py-2 text-center">{{ $inventory->quantity }} productos</td>
              <td class="whitespace-nowrap px-4 py-2 text-center">S/. {{ $inventory->unit_price }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
