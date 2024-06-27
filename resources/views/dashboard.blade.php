{{-- Contenido --}}
<div class="mb-4">
  <div class="flex flex-wrap gap-2">
    <!-- Cantidad de Productos -->
    <div class="flex flex-1 items-center rounded-lg bg-blue-500 p-2 text-white shadow-md">
      <div class="mr-2">
        <i class="fas fa-box fa-lg"></i>
      </div>
      <div wire:poll="updateTotalQuantity">
        <h2 class="text-sm font-semibold">Productos</h2>
        <p class="mt-1 text-xl">{{ $totalQuantity }}</p>
      </div>
    </div>

    <!-- Cantidad de Reservas -->
    <div class="flex flex-1 items-center rounded-lg bg-green-500 p-2 text-white shadow-md">
      <div class="mr-2">
        <i class="fas fa-cubes fa-lg"></i>
      </div>
      <div wire:poll="updateTotalReservations">
        <h2 class="text-sm font-semibold">Reservas</h2>
        <p class="mt-1 text-xl">{{ $totalReservations }}</p>
      </div>
    </div>

    <!-- Ganancias -->
    <div class="flex flex-1 items-center rounded-lg bg-yellow-500 p-2 text-white shadow-md">
      <div class="mr-2">
        <i class="fas fa-dollar-sign fa-lg"></i>
      </div>
      <div wire:poll="updateTotalRevenue">
        <h2 class="text-sm font-semibold">Ganancias</h2>
        <p class="mt-1 text-xl">S/. {{ $totalRevenue }}</p>
      </div>
    </div>
  </div>
  <div class="p-4 shadow-md mt-4">
    Fecha de Actualización 29/06/2024 (GRAFICOS)
  </div>
</div>
