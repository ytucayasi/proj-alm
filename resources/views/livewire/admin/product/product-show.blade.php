<div>
  {{-- Encabezado --}}
  <x-slot name="header">
    Producto #{{ $this->form->product->id }}
  </x-slot>

  {{-- Contenido --}}

  <div class="flex flex-col flex-wrap gap-8 md:flex-nowrap">
    <div class="w-full">
      <div class="mb-4 flex flex-wrap justify-between gap-2">
        <span class="text-xl font-semibold leading-tight text-gray-800">Datos generales</span>
      </div>
      <ul class="flex flex-wrap gap-2 sm:flex-row sm:flex-nowrap">
        <li class="w-full rounded-md bg-gray-100 px-4 py-2">Categoría: {{ $this->form->product->category ? $this->form->product->category->name : 'Sin Asignar' }}</li>
        <li class="w-full rounded-md bg-gray-100 px-4 py-2">Nombre: {{ $this->form->product->name }}</li>
        <li class="w-full rounded-md bg-gray-100 px-4 py-2">Descripción: {{ $this->form->product->description ? $this->form->product->description : 'N.A.' }}</li>
        <li class="w-full rounded-md bg-gray-100 px-4 py-2">
          Estado: <span
            class="{{ $this->form->product->state ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
            {{ $this->form->product->state ? 'Activo' : 'Inactivo' }}
          </span>
        </li>
      </ul>
    </div>
    <div class="w-full">
      <div class="mb-4 flex flex-wrap justify-between gap-2">
        <span class="text-xl font-semibold leading-tight text-gray-800">Variaciones</span>
      </div>
      <div class="mb-4 flex flex-wrap justify-between gap-2">
        <div class="flex items-center justify-center gap-2">
          <input type="text"
            class="form-input rounded-md border-gray-300 bg-white px-4 py-2 placeholder-gray-500 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
            placeholder="Buscar..." wire:model.live="searchVariation">
        </div>
      </div>
      <table class="min-w-full overflow-hidden rounded-lg bg-white leading-normal shadow-md dark:bg-gray-900">
        <thead>
          <tr>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Unidad de Medida</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Cantidad</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Precio</th>
          </tr>
        </thead>
        <tbody wire:poll>
          @foreach ($variations as $variation)
            <tr class="hover:bg-gray-100 dark:hover:bg-gray-800">
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                {{ $variation->unit->abbreviation }}</td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                {{ $variation->quantity_base }}</td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                S/. {{ $variation->price_base }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="my-4">
        {{ $variations->links() }}
      </div>
    </div>
    <div class="w-full">
      <div class="mb-4 flex flex-wrap justify-between gap-2">
        <span class="text-xl font-semibold leading-tight text-gray-800">Inventarios</span>
      </div>
      <div class="mb-4 flex flex-wrap justify-between gap-2">
        <div class="flex items-center justify-center gap-2">
          <input type="text"
            class="form-input rounded-md border-gray-300 bg-white px-4 py-2 placeholder-gray-500 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
            placeholder="Buscar..." wire:model.live="searchInventory">
        </div>
      </div>
      <table class="min-w-full overflow-hidden rounded-lg bg-white leading-normal shadow-md dark:bg-gray-900">
        <thead>
          <tr>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              ID</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Cantidad</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Unidad de Medida</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Fecha de Ingreso</th>
          </tr>
        </thead>
        <tbody wire:poll>
          @foreach ($inventories as $inventory)
            <tr class="{{ $inventory->movement_type == '1' ? 'bg-green-200' : 'bg-red-200' }}">
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                {{ $inventory->id }}</td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                {{ $inventory->quantity }}</td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                {{ $inventory->unit->abbreviation }}</td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                {{ $inventory->created_at }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="my-4">
        {{ $inventories->links() }}
      </div>
    </div>
  </div>
</div>
