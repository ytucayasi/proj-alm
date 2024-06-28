<!-- resources/views/livewire/admin/inventory/inventory-page.blade.php -->
<div>
  {{-- Encabezado --}}
  <x-slot name="header">
    {{ __('Inventarios') }}
  </x-slot>

  {{-- Contenido --}}
  <div>
    <div class="mb-4 flex flex-wrap justify-between gap-2">
      <div class="flex items-center justify-center gap-2">
        <input type="text"
          class="form-input rounded-md border-gray-300 bg-white px-4 py-2 placeholder-gray-500 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
          placeholder="Buscar..." wire:model.live="search">
        <select wire:model.live="perPage"
          class="form-select rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
      </div>
      <div class="flex items-center justify-center gap-2">
        <button
          class="flex items-center gap-2 rounded-md bg-green-500 px-4 py-2 text-white shadow-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300"
          wire:click.prevent="openModal('{{ $modalCreateOrUpdate }}')">
          <i class="fas fa-plus-square fa-lg flex h-7 w-5 items-center justify-center"></i> Crear
        </button>
        <button
          class="rounded-md bg-blue-500 px-4 py-2 text-white shadow-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300"
          wire:click="toggleViewMode">
          @if ($viewMode === 'table')
            <i class="fas fa-grip-horizontal fa-lg flex h-7 w-5 items-center justify-center"></i>
          @else
            <i class="fas fa-table fa-lg flex h-7 w-5 items-center justify-center"></i>
          @endif
        </button>
      </div>
    </div>
    @if ($viewMode === 'table')
      <table class="min-w-full overflow-hidden rounded-lg bg-white leading-normal shadow-md">
        <thead>
          <tr>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              ID</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Producto</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Cantidad</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Precio Unitario</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Unidad</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Fecha de Creación</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($inventories as $inventory)
            <tr class="{{ $inventory->movement_type == '1' ? 'bg-green-200' : 'bg-red-200' }}">
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">{{ $inventory->id }}</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                <a class="text-blue-500 underline transition duration-300 ease-in-out hover:text-blue-700 hover:no-underline"
                  href="/products/{{ $inventory->product->id }}"
                  wire:navigate>{{ $inventory->product ? $inventory->product->name : 'Sin Asignar' }}</a>
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $inventory->quantity }} productos</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $inventory->movement_type == 2 && $inventory->type_action != 2 ? '-' : 'S/. ' . $inventory->unit_price }}</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $inventory->unit->name }}
                <span class="font-bold">
                  ({{ $inventory->unit->abbreviation }})
                </span>
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $inventory->created_at }}</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center">
                @if ($inventory->type_action != 2)
                  <button
                    class="rounded bg-yellow-400 px-2 py-1 font-bold text-white hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                    wire:click="edit({{ $inventory->id }})"><i class="fas fa-edit"></i></button>
                  <button
                    class="rounded bg-red-400 px-2 py-1 font-bold text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300"
                    wire:click="alertDelete({{ $inventory->id }})"><i class="fas fa-trash"></i></button>
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
    @else
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($inventories as $inventory)
          <div class="overflow-hidden rounded-lg bg-white shadow-lg">
            <div class="p-6">
              <h3 class="mb-2 text-lg font-semibold text-gray-900">
                {{ $inventory->product ? $inventory->product->name : 'Sin Asignar' }}</h3>
              <span class="block text-sm text-gray-600">Cantidad: {{ $inventory->quantity }}</span>
              <p class="block text-sm text-gray-600">
                Movimiento:
                <span
                  class="{{ $inventory->movement_type == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                  {{ $inventory->movement_type == 1 ? 'Entrada' : 'Salida' }}
                </span>
              </p>
              <span class="block text-sm text-gray-600">Precio Unitario: {{ $inventory->unit_price }}</span>
              <span class="block text-sm text-gray-600">Unidad: {{ $inventory->unit->abbreviation }}</span>
              @if ($inventory->type_action != 2)
                <div class="mt-2 text-right">
                  <button
                    class="rounded bg-yellow-400 px-2 py-1 font-bold text-white hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                    wire:click="edit({{ $inventory->id }})"><i class="fas fa-edit"></i></button>
                  <button
                    class="rounded bg-red-400 px-2 py-1 font-bold text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300"
                    wire:click="alertDelete({{ $inventory->id }})"><i class="fas fa-trash"></i></button>
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif
    <div class="mt-4">
      {{ $inventories->links() }}
    </div>
    <x-modal maxWidth="lg" name="{{ $modalCreateOrUpdate }}" :show="false" focusable>
      <form wire:submit.prevent="save" class="p-6">
        <div class="text-lg font-medium text-gray-900">{{ $form->id ? 'Editar Inventario' : 'Crear Inventario' }}</div>
        @if (!$form->id)
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Producto</label>
            <select wire:model="form.product_id" class="form-select mt-1 block w-full rounded-md shadow-sm">
              <option>Seleccionar Producto</option>
              @foreach ($products as $product)
                <option value="{{ $product->id }}">{{ $product->name }}</option>
              @endforeach
            </select>
            @error('form.product_id')
              <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
          </div>
        @endif
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Cantidad</label>
          <input type="number" step="0.01" wire:model="form.quantity" class="form-input mt-1 block w-full rounded-md shadow-sm">
          @error('form.quantity')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Tipo de Movimiento</label>
          <select wire:model.live="form.movement_type" class="form-select mt-1 block w-full rounded-md shadow-sm">
            <option value="1">Entrada</option>
            <option value="2">Salida</option>
          </select>
          @error('form.movement_type')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        @if ($form->movement_type == 1)
          <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Precio Unitario</label>
            <input type="number" step="0.01" wire:model="form.unit_price"
              class="form-input mt-1 block w-full rounded-md shadow-sm">
            @error('form.unit_price')
              <span class="text-sm text-red-500">{{ $message }}</span>
            @enderror
          </div>
        @endif
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Unidad</label>
          <select wire:model="form.unit_id" class="form-select mt-1 block w-full rounded-md shadow-sm">
            <option>Seleccionar Unidad de Medida</option>
            @foreach ($units as $unit)
              <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->abbreviation }})</option>
            @endforeach
          </select>
          @error('form.unit_id')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Descripción</label>
          <textarea wire:model="form.description" class="form-input mt-1 block w-full rounded-md shadow-sm"></textarea>
          @error('form.description')
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
