{{-- Contenido --}}
<div class="mb-4">
  <div class="flex flex-wrap gap-4 md:flex-nowrap">
    <div class="flex w-full justify-between rounded-md bg-orange-500 p-2">
      <div class="flex items-center justify-center text-white">
        <i class="fas fa-box fa-lg mr-1"></i>
        <h2 class="text-sm font-semibold">
          <span class="text-lg">
            {{ $countProducts }}
          </span>
          Productos
        </h2>
      </div>
      <div class="flex flex-col items-start justify-center rounded-md bg-orange-600 p-2 text-white">
        <h3 class="text-xs font-semibold"><span class="text-sm">{{ $countActive }}</span> Activos</h3>
        <h3 class="text-xs font-semibold"><span class="text-sm">{{ $countInventory }}</span> Inventario</h3>
      </div>
    </div>
    <div class="flex w-full flex-col items-center justify-center gap-2 rounded-md bg-cyan-500 p-2">
      <div class="flex items-center justify-center text-white">
        <i class="fas fa-box fa-lg mr-1"></i>
        <h2 class="flex items-center gap-1 text-sm font-semibold">
          <span class="text-lg">
            {{ $countReservations }}
          </span>
          <p>Reservas</p>
        </h2>
      </div>
      <div class="flex items-center gap-2 text-white">
        <h3 class="rounded-md bg-cyan-600 p-2 text-center text-xs font-semibold">
          <i class="fas fa-check-circle text-xs"></i>
          <span class="text-xs">{{ $countReservationR }}</span>
        </h3>
        <h3 class="rounded-md bg-cyan-600 p-2 text-center text-xs font-semibold">
          <i class="fas fa-spinner text-xs"></i>
          <span class="text-xs">{{ $countReservationE }}</span>
        </h3>
        <h3 class="rounded-md bg-cyan-600 p-2 text-center text-xs font-semibold">
          <i class="fas fa-hourglass-half text-xs"></i>
          <span class="text-xs">{{ $countReservationPo }}</span>
        </h3>
        <h3 class="rounded-md bg-cyan-600 p-2 text-center text-xs font-semibold">
          <i class="fas fa-pause-circle text-xs"></i>
          <span class="text-xs">{{ $countReservationPe }}</span>
        </h3>
      </div>
    </div>
    <div class="flex w-full justify-between rounded-md bg-purple-500 p-2">
      <div class="flex items-center justify-center text-white">
        <i class="fas fa-box fa-lg mr-1"></i>
        <h2 class="text-sm font-semibold">
          <span class="text-lg">
            S/. {{ $total_profits }}
          </span>
          de ganancia
        </h2>
      </div>
      <div class="flex items-center gap-2 text-white">
        <h3 class="w-full rounded-md bg-purple-600 p-2 text-center text-sm font-semibold">
          <span class="text-lg">{{ $total_percentage }}</span>
          <i class="fas fa-percentage"></i>
        </h3>
      </div>
    </div>
  </div>
  <div class="mt-4 rounded-lg bg-white p-2 text-gray-700 shadow-md">
    <p class="text-1xl font-semibold">Reservaciones para Hoy</p>
    <div class="flex flex-wrap">
      @foreach ($todayReservations as $reservation)
        <div class="w-full p-2 lg:w-1/3">
          <div x-data="{ currentSlide: 0 }" class="relative w-full overflow-hidden">
            <div class="flex transition-transform duration-500"
              :style="{ transform: 'translateX(-' + currentSlide * 100 + '%)' }">
              @foreach ($reservation->companies as $company)
                <div class="min-w-full p-4">
                  <div class="relative rounded-lg bg-gray-100 p-4 shadow">
                    <div class="absolute right-7 top-6 flex flex-col">
                      <a class="text-blue-500 underline transition duration-300 ease-in-out hover:text-blue-700 hover:no-underline"
                        href="/reservations/{{ $reservation->id }}" wire:navigate>#{{ $reservation->id }}</a>
                      <span class="text-xs font-semibold">Total: {{ $reservation->total_pack }}</span>
                    </div>
                    <h3 class="text-lg font-semibold">{{ $company->company->name }}</h3>
                    <p class="text-sm">Pack: {{ $company->pack }}</p>
                    <p class="text-sm">Costo por Pack: S/ {{ number_format($company->cost_pack, 2) }}</p>
                    <p class="text-sm">Total: S/ {{ number_format($company->total_pack, 2) }}</p>
                  </div>
                </div>
              @endforeach
            </div>
            <button
              @click="currentSlide = (currentSlide === 0) ? {{ $reservation->companies->count() }} - 1 : currentSlide - 1"
              class="absolute left-0 top-1/2 -translate-y-1/2 transform rounded-full bg-gray-200 p-2 shadow-md">
              &#10094;
            </button>
            <button
              @click="currentSlide = (currentSlide === {{ $reservation->companies->count() }} - 1) ? 0 : currentSlide + 1"
              class="absolute right-0 top-1/2 -translate-y-1/2 transform rounded-full bg-gray-200 p-2 shadow-md">
              &#10095;
            </button>
          </div>
        </div>
      @endforeach
    </div>
  </div>
{{--   <div class="mt-4 rounded-lg bg-white p-2 text-gray-700 shadow-md">
    <!-- Listar Productos con Stock Bajo -->
    <div>
      <h2 class="mb-4 text-1xl font-semibold">Productos con Stock Bajo</h2>
      <!-- Buscar Productos -->
      <div class="mb-4">
        <input type="text" wire:model.live="search"
          class="w-full rounded-lg border px-4 py-2 shadow-sm focus:border-blue-300 focus:outline-none focus:ring"
          placeholder="Buscar productos..." />
      </div>
      <table class="w-full table-auto">
        <thead>
          <tr class="bg-gray-100">
            <th class="px-4 py-2">Nombre</th>
            <th class="px-4 py-2">Cantidad Base</th>
            <th class="px-4 py-2">Stock Mínimo</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lowStockProducts as $variation)
            <tr>
              <td class="border px-4 py-2">{{ $variation->product_name }}</td>
              <td class="border px-4 py-2">{{ $variation->quantity_base }}</td>
              <td class="border px-4 py-2">{{ $variation->stock_min }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="py-4 text-center">No hay productos con stock bajo.</td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <!-- Paginación -->
      <div class="mt-4">
        {{ $lowStockProducts->links() }}
      </div>
    </div>
  </div> --}}
</div>
