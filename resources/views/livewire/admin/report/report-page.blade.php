<div>
  <div class="flex justify-between">
    <div class="mb-4 flex space-x-2">
      <button wire:click="setToday"
        class="rounded-lg bg-blue-500 px-2 py-2 text-xs font-semibold text-white shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
        <i class="fas fa-calendar-day"></i> Hoy
      </button>
      <button wire:click="setWeek"
        class="rounded-lg bg-green-500 px-2 py-2 text-xs font-semibold text-white shadow-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
        <i class="fas fa-calendar-week"></i> Semana
      </button>
      <button wire:click="setThreeWeeks"
        class="rounded-lg bg-yellow-500 px-2 py-2 text-xs font-semibold text-white shadow-md hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2">
        <i class="fas fa-calendar-alt"></i> 3 Semanas
      </button>
      <button wire:click="setMonth"
        class="rounded-lg bg-purple-500 px-2 py-2 text-xs font-semibold text-white shadow-md hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2">
        <i class="fas fa-calendar"></i> Mes
      </button>
      <button wire:click="setThreeMonths"
        class="rounded-lg bg-red-500 px-2 py-2 text-xs font-semibold text-white shadow-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
        <i class="fas fa-calendar-alt"></i> 3 Meses
      </button>
      <button wire:click="setPreviousYears"
        class="rounded-lg bg-teal-500 px-2 py-2 text-xs font-semibold text-white shadow-md hover:bg-teal-600 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2">
        <i class="fas fa-calendar-alt"></i> Todos
      </button>
    </div>
    <div class="mb-4 flex space-x-2">
      <button wire:click="reportG()"
        class="rounded-lg bg-slate-500 px-2 py-2 text-xs font-semibold text-white shadow-md hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
        <i class="fas fa-chart-pie"></i> Gráficos
      </button>
      {{--       <button wire:click="setThreeMonths"
        class="rounded-lg bg-slate-500 px-2 py-2 text-xs font-semibold text-white shadow-md hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
        <i class="far fa-chart-bar"></i> Productos
      </button>
      <button wire:click="setThreeMonths"
        class="rounded-lg bg-slate-500 px-2 py-2 text-xs font-semibold text-white shadow-md hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
        <i class="far fa-chart-bar"></i> Areas
      </button> --}}
    </div>
  </div>
  <div class="mb-12 grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">
    <div class="flex flex-col items-start justify-center">
      <label for="search" class="block text-sm font-medium text-gray-700">Buscar</label>
      <input type="text" wire:model.live="search" id="search"
        class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
        placeholder="Buscar por producto">
    </div>
    <div class="flex flex-col items-start justify-center">
      <label for="area" class="block text-sm font-medium text-gray-700">Area</label>
      <select wire:model.live="area_id" id="area"
        class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
        <option value="">Seleccionar Area</option>
        @foreach ($areas as $area)
          <option value="{{ $area->id }}">{{ $area->name }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex flex-col items-start justify-center">
      <label for="company" class="block text-sm font-medium text-gray-700">Empresa</label>
      <select wire:model.live="company_id" id="company"
        class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
        <option value="">Seleccionar Empresa</option>
        @foreach ($companies as $company)
          <option value="{{ $company->id }}">{{ $company->name }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label for="date_range" class="block text-sm font-medium text-gray-700">Rango de Fecha</label>
      <div class="mt-1 flex w-full flex-col space-y-2">
        <input type="datetime-local" wire:model.live="start_date"
          class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
          id="start_date">
        <input type="datetime-local" wire:model.live="end_date"
          class="block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm"
          id="end_date">
      </div>
    </div>
  </div>
  <div class="mb-4 flex justify-end space-x-2">
    <button wire:click="export"
      class="rounded-lg bg-indigo-600 px-4 py-2 font-semibold text-white shadow-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
      <i class="fas fa-file-export"></i> Exportar
    </button>
    <button wire:click="clearFilters"
      class="rounded-lg bg-gray-500 px-4 py-2 font-semibold text-white shadow-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
      <i class="fas fa-broom"></i> Limpiar
    </button>
  </div>
  <div class="mb-4">
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-5">
      <div class="rounded-lg bg-white p-4 shadow">
        <div class="text-sm font-medium text-gray-500">Total Pack</div>
        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $totalPack }}</div>
      </div>
      <div class="rounded-lg bg-white p-4 shadow">
        <div class="text-sm font-medium text-gray-500">Total Productos</div>
        <div class="mt-1 text-lg font-semibold text-gray-900">{{ $totalProducts }}</div>
      </div>
      <div class="rounded-lg bg-white p-4 shadow">
        <div class="text-sm font-medium text-gray-500">Total Pagado</div>
        <div class="mt-1 text-lg font-semibold text-gray-900">S/. {{ number_format($totalPaid, 2) }}</div>
      </div>
      <div class="rounded-lg bg-white p-4 shadow">
        <div class="text-sm font-medium text-gray-500">Costo Total</div>
        <div class="mt-1 text-lg font-semibold text-gray-900">S/. {{ number_format($totalCost, 2) }}</div>
      </div>
      <div class="rounded-lg bg-white p-4 shadow">
        <div class="text-sm font-medium text-gray-500">Total Ganado</div>
        <div class="{{ $totalGanado < 0 ? 'text-red-600' : 'text-green-600' }} mt-1 text-lg font-semibold">S/.
          {{ number_format($totalGanado, 2) }}</div>
      </div>
    </div>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full bg-white">
      <thead>
        <tr>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            <input type="checkbox" wire:model.live="selectAll">
          </th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            ID</th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            Empresas</th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            Status</th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            Payment Status</th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            Fecha Órden</th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            Fecha Ejecución</th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            Costo Total</th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            Pack</th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            Total Productos</th>
          <th
            class="border-b border-gray-200 bg-gray-50 px-4 py-2 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
            Total Pagado</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200 bg-white text-xs">
        @foreach ($reservations as $reservation)
          <tr>
            <td class="px-4 py-2 text-center">
              <input type="checkbox" wire:model.live="selectedReservations" value="{{ $reservation->id }}"
                wire:change="">
            </td>
            <td class="px-4 py-2 text-center">{{ $reservation->id }}</td>
            <td class="px-4 py-2 text-center">
              {{ implode(', ', $reservation->companies->pluck('company.name')->toArray()) }}</td>
            <td class="px-4 py-2 text-center">
              @switch($reservation->status)
                @case(1)
                  Realizado
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
            </td>
            <td class="px-4 py-2 text-center">{{ $reservation->payment_status == 1 ? 'Pagado' : 'Pendiente de Pago' }}
            </td>
            <td class="px-4 py-2 text-center">
              {{ \Carbon\Carbon::parse($reservation->order_date)->format('Y/m/d H:i') }}
            </td>
            <td class="px-4 py-2 text-center">
              {{ \Carbon\Carbon::parse($reservation->execution_date)->format('Y/m/d H:i') }}</td>
            <td class="px-4 py-2 text-center">S/. {{ $reservation->total_cost }}</td>
            <td class="px-4 py-2 text-center">{{ $reservation->people_count }} pack</td>
            <td class="px-4 py-2 text-center">{{ $reservation->total_products }}</td>
            <td class="px-4 py-2 text-center">S/. {{ $reservation->total_pack }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="mt-4">
      {{ $reservations->links() }}
    </div>
  </div>
</div>
