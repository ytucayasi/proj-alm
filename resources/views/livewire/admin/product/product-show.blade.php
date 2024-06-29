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
        <li class="w-full rounded-md bg-gray-100 px-4 py-2">Categoría:
          {{ $this->form->product->category ? $this->form->product->category->name : 'Sin Asignar' }}</li>
        <li class="w-full rounded-md bg-gray-100 px-4 py-2">Nombre: {{ $this->form->product->name }}</li>
        <li class="w-full rounded-md bg-gray-100 px-4 py-2">Descripción:
          {{ $this->form->product->description ? $this->form->product->description : 'N.A.' }}</li>
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
            {{-- <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Acciones</th> --}}
          </tr>
        </thead>
        <tbody wire:poll>
          @foreach ($variations as $variation)
            <tr>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                {{ $variation->unit->name }}
                <span class="font-bold">
                  ({{ $variation->unit->abbreviation }})
                </span>
              </td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                <span class="font-bold">{{ $variation->quantity_base }}</span>
              </td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                <span class="font-bold">
                  {{ $variation->price_base == 0 ? '-' : 'S/.' . $variation->price_base }}
                </span>
              </td>
              {{-- <td class="border-b border-gray-200 px-5 py-2 text-center">
                <button
                  class="rounded bg-yellow-400 px-2 py-1 font-bold text-white hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                  wire:click="editVariation({{ $variation->id }})"><i class="fas fa-edit"></i></button>
              </td> --}}
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
              Precio</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Unidad de Medida</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Fecha de Ingreso</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
              Acciones</th>
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
                @if (($inventory->type_action != 2 && $inventory->movement_type == '2') || $inventory->type == 2)
                  -
                @else
                  S/. {{ $inventory->unit_price }}
                @endif
              </td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                {{ $inventory->unit->name }}
                <span class="font-bold">
                  ({{ $inventory->unit->abbreviation }})
                </span>
              </td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                {{ $inventory->created_at }}</td>
              <td
                class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900 dark:border-gray-700 dark:text-gray-300">
                @if ($inventory->type_action != 2)
                  -
                @else
                  <button
                    class="rounded bg-orange-400 px-2 py-1 font-bold text-white hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-300"
                    wire:click="reservation({{ $inventory->reservation_id }})"><i
                      class="fas fa-external-link-alt"></i></button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="my-4">
        {{ $inventories->links() }}
      </div>
      <x-modal maxWidth="sm" name="{{ $modalCreateOrUpdate }}" :show="false" focusable>
        <form wire:submit.prevent="saveVariation" class="p-6">
          <div class="text-lg font-medium text-gray-900">{{ $form->id ? 'Editar Producto' : 'Crear Producto' }}</div>
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Price</label>
            <input type="number" step="0.01" wire:model="form.price_base"
              class="form-input mt-1 block w-full rounded-md shadow-sm">
            @error('form.price_base')
              <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
          </div>
          <div class="mt-6 flex justify-end">
            <x-secondary-button wire:click="closeModal('{{ $modalCreateOrUpdate }}')">
              {{ __('Cancelar') }}
            </x-secondary-button>
            <x-primary-button class="ms-3" wire:loading.class="opacity-50" wire:loading.attr="disabled">
              {{ __('Guardar') }}
            </x-primary-button>
          </div>
        </form>
      </x-modal>
    </div>
  </div>
</div>
