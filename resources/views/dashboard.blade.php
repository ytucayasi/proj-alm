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
    <p class="text-xs">Fecha de Actualización: 29/06/2024 (GRAFICOS)</p>
  </div>
</div>
{{--           <i class="fas fa-box fa-lg mr-1"></i>
          <h2 class="text-sm font-semibold">Productos {{ $countProducts }}</h2> --}}
