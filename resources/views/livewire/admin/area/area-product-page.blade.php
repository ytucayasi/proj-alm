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
        <option value="10">10</option>
        <option value="20">20</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
      <div class="flex w-full justify-center">
        <svg wire:loading class="animate-spin text-gray-300" viewBox="0 0 64 64" fill="none"
          xmlns="http://www.w3.org/2000/svg" width="24" height="24">
          <path
            d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
          <path
            d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
            stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-900">
          </path>
        </svg>
        <span wire:loading class="ms-2">
          Cargando...
        </span>
      </div>
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
      @forelse ($areaProducts as $areaProduct)
        <tr class="hover:bg-gray-100">
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">{{ $areaProduct->id }}</td>
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
            {{ $areaProduct->product->name }}</td>
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
            {{ $areaProduct->unit->abbreviation }}</td>
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">{{ $areaProduct->quantity }}
          </td>
          <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
            {{ $form->viewVariation($areaProduct->product_id, $areaProduct->unit_id)->price_base }}
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
      @empty
        <tr>
          <td colspan="6" class="border-b px-4 py-2 text-center">
            <p class="text-sm text-gray-500">No se registraron aún datos</p>
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
  <div class="mt-4">
    {{ $areaProducts->links() }}
  </div>
  <x-modal maxWidth="lg" name="{{ $modalCreateOrUpdate }}" :show="false" focusable>
    <form wire:submit.prevent="save" class="p-6">
      <div class="text-lg font-medium text-gray-900">{{ $form->id ? 'Editar Producto' : 'Agregar Producto' }}</div>
      @if (!$form->id)
        <div class="mt-4" x-data="{ open: false }">
          <label class="block text-sm font-medium text-gray-700">Producto</label>
          <input type="text" wire:model.live="productSearch" @focus="open = true"
            @blur="setTimeout(() => open = false, 200)" class="form-input mt-1 block w-full rounded-md shadow-sm"
            placeholder="Buscar producto...">
          @if ($productSearch)
            <ul class="mt-1 max-h-60 overflow-auto rounded-md border border-gray-300 bg-white shadow-lg" x-show="open">
              @forelse ($searchResults as $product)
                <li wire:click="selectProduct({{ $product->id }}, '{{ $product->name }}')"
                  class="cursor-pointer px-4 py-2 hover:bg-gray-200">
                  {{ $product->name }}
                </li>
              @empty
                <li class="px-4 py-2 text-gray-500">No se encontraron productos</li>
              @endforelse
            </ul>
          @endif
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
      @endif
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700">Cantidad</label>
        <input type="number" step="0.01" wire:model="form.quantity"
          class="form-input mt-1 block w-full rounded-md shadow-sm">
        @error('form.quantity')
          <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
      </div>
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700">Precio</label>
        <input readonly disabled type="number" wire:model="form.price" step="0.01"
          class="form-input mt-1 block w-full cursor-not-allowed rounded-md bg-gray-100 opacity-70 shadow-sm">
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
