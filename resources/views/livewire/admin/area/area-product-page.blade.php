<div>
  <x-slot name="header">
    {{ __('Productos de Área: ' . $form->area->name) }}
  </x-slot>

  <div class="mb-4 flex flex-wrap justify-between gap-2">
    <div class="flex items-center justify-center gap-2">
      <input type="text"
        class="form-input rounded-md border-gray-300 bg-white px-4 py-2 placeholder-gray-500 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500"
        placeholder="Buscar..." wire:model.live="search">
      <select wire:model.live="perPage"
        class="form-select rounded-md border-gray-300 bg-white shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500">
        <option value="5">5</option>
        <option value="10">10</option>
        <option value="15">15</option>
        <option value="20">20</option>
      </select>
    </div>
    <div class="flex items-center justify-center gap-2">
      <button
        class="flex items-center gap-2 rounded-md bg-green-500 px-4 py-2 text-white shadow-lg hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300"
        wire:click.prevent="openModal('{{ $modalCreateOrUpdate }}')">
        <i class="fas fa-plus-square fa-lg flex h-7 w-5 items-center justify-center"></i> Agregar Producto
      </button>
    </div>
  </div>

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
          Unidad</th>
        <th
          class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
          Cantidad</th>
        <th
          class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
          Precio</th>
        <th
          class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
          Acciones</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($areaProducts as $areaProduct)
        <tr class="hover:bg-gray-100">
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">{{ $areaProduct->id }}</td>
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
            {{ $areaProduct->product->name }}</td>
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
            {{ $areaProduct->unit->abbreviation }}</td>
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">{{ $areaProduct->quantity }}
          </td>
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">{{ $areaProduct->price }}
          </td>
          <td class="border-b border-gray-200 px-5 py-2 text-center">
            <button
              class="rounded bg-yellow-400 px-2 py-1 font-bold text-white hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-300"
              wire:click="edit({{ $areaProduct->id }})"><i class="fas fa-edit"></i></button>
            <button
              class="rounded bg-red-400 px-2 py-1 font-bold text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300"
              wire:click="delete({{ $areaProduct->id }})"><i class="fas fa-trash"></i></button>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-4">
    {{ $areaProducts->links() }}
  </div>

  <x-modal maxWidth="lg" name="{{ $modalCreateOrUpdate }}" :show="false" focusable>
    <form wire:submit.prevent="save" class="p-6">
      <div class="text-lg font-medium text-gray-900">{{ $form->id ? 'Editar Producto' : 'Agregar Producto' }}</div>
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700">Producto</label>
        <select wire:model.live="form.product_id" class="form-select mt-1 block w-full rounded-md shadow-sm">
          <option value="">Seleccione un producto</option>
          @foreach ($products as $product)
            <option value="{{ $product->id }}">{{ $product->name }}</option>
          @endforeach
        </select>
        @error('form.product_id')
          <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
      </div>
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700">Unidad</label>
        <select wire:model.live="form.unit_id" class="form-select mt-1 block w-full rounded-md shadow-sm">
          @if ($units->count() == 0)
            <option>Seleccione primero un producto</option>
          @endif
          @foreach ($units as $unit)
            <option value="{{ $unit->id }}">{{ $unit->name }} - {{ $unit->abbreviation }}</option>
          @endforeach
        </select>
        @error('form.unit_id')
          <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
      </div>
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700">Cantidad</label>
        <input type="number" step="0.01" wire:model="form.quantity" class="form-input mt-1 block w-full rounded-md shadow-sm">
        @error('form.quantity')
          <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
      </div>
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700">Precio</label>
        <input type="number" wire:model="form.price" step="0.01"
          class="form-input mt-1 block w-full rounded-md shadow-sm">
        @error('form.price')
          <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
      </div>
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700">Stock</label>
        <input readonly disabled type="number" wire:model="form.stock"
          class="form-input mt-1 block w-full cursor-not-allowed rounded-md bg-gray-100 opacity-70 shadow-sm">
        @error('form.stock')
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
