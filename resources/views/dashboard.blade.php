
  {{-- Contenido --}}
  <div x-data="{ showFilters: false }" class="p-4">
    <button @click="showFilters = !showFilters"
      class="mb-4 flex transform items-center space-x-2 rounded-md bg-purple-500 px-4 py-2 font-semibold text-white shadow-lg transition duration-300 ease-in-out hover:scale-105 hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-300">
      <i class="fas fa-filter"></i>
      <span>Filtrar</span>
    </button>
    <div x-show="showFilters"
      class="mb-6 flex flex-row flex-wrap justify-between space-y-4 md:flex-nowrap md:space-x-4 md:space-y-0">
      <!-- Input de Rango de Fecha -->
      <div class="flex w-full flex-col space-y-2">
        <label for="date-range" class="w-full font-semibold text-gray-700">Fecha de Entrada:</label>
        <input id="start-date" type="date"
          class="w-full rounded-md border shadow-sm focus:border-blue-300 focus:outline-none focus:ring">
        <label for="date-range" class="w-full font-semibold text-gray-700">Fecha de Salida:</label>
        <input id="end-date" type="date"
          class="w-full rounded-md border shadow-sm focus:border-blue-300 focus:outline-none focus:ring">
      </div>

      <!-- Selectores de Filtros -->
      <div class="flex w-full flex-col space-y-2">
        <div class="flex flex-col justify-start space-y-2">
          <label for="filter1" class="font-semibold text-gray-700">Filtro 1:</label>
          <select id="filter1"
            class="rounded-md border shadow-sm focus:border-blue-300 focus:outline-none focus:ring">
            <option value="">Seleccione una opción</option>
            <option value="1">Opción 1</option>
            <option value="2">Opción 2</option>
          </select>
        </div>

        <div class="flex flex-col justify-start space-y-2">
          <label for="filter1" class="font-semibold text-gray-700">Filtro 1:</label>
          <select id="filter1"
            class="rounded-md border shadow-sm focus:border-blue-300 focus:outline-none focus:ring">
            <option value="">Seleccione una opción</option>
            <option value="1">Opción 1</option>
            <option value="2">Opción 2</option>
          </select>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
      <!-- Cantidad de Productos -->
      <div class="flex items-center rounded-lg bg-blue-500 p-6 text-white shadow-md">
        <div class="mr-4">
          <i class="fas fa-box fa-2x"></i>
        </div>
        <div wire:poll="updateTotalQuantity">
          <h2 class="text-lg font-semibold">Productos</h2>
          <p class="mt-2 text-3xl">{{ $totalQuantity }}</p>
        </div>
      </div>

      <!-- Cantidad de Reservas -->
      <div class="flex items-center rounded-lg bg-green-500 p-6 text-white shadow-md">
        <div class="mr-4">
          <i class="fas fa-cubes fa-2x"></i>
        </div>
        <div>
          <h2 class="text-lg font-semibold">Reservas (Falta)</h2>
          <p class="mt-2 text-3xl">0</p>
        </div>
      </div>

      <!-- Egresos -->
      <div class="flex items-center rounded-lg bg-yellow-500 p-6 text-white shadow-md">
        <div class="mr-4">
          <i class="fas fa-dollar-sign fa-2x"></i>
        </div>
        <div wire:poll="updateTotalRevenue">
          <h2 class="text-lg font-semibold">Egresos</h2>
          <p class="mt-2 text-3xl">S/. {{ $totalRevenue }}</p>
        </div>
      </div>

      <!-- Entradas (Cantidad de Stock * Cantidad) -->
      <div class="flex items-center rounded-lg bg-red-500 p-6 text-white shadow-md">
        <div class="mr-4">
          <i class="fas fa-dollar-sign fa-2x"></i>
        </div>
        <div>
          <h2 class="text-lg font-semibold">Ingresos (Falta)</h2>
          <p class="mt-2 text-3xl">S/. 0</p>
        </div>
      </div>
    </div>

  </div>