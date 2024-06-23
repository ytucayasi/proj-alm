<div>
  {{-- Encabezado --}}
  <x-slot name="header">
    {{ __('Productos') }}
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
              Nombre</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Categoría</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Estado</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($products as $product)
            <tr>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">{{ $product->id }}</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $product->name }}
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $product->category ? $product->category->name : 'Sin Asignar' }}</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                <span
                  class="{{ $product->state == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                  {{ $product->state == 1 ? 'Disponible' : 'Agotado' }}
                </span>
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center">
                <div class="flex gap-1">
                  <button
                    class="rounded bg-yellow-400 px-2 py-1 font-bold text-white hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                    wire:click="edit({{ $product->id }})"><i class="fas fa-edit"></i></button>
                  <button
                    class="rounded bg-red-400 px-2 py-1 font-bold text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300"
                    wire:click="alertDelete({{ $product->id }})"><i class="fas fa-trash"></i></button>
                  <button
                    class="rounded bg-cyan-400 px-2 py-1 font-bold text-white hover:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300"
                    wire:click="view({{ $product->id }})"><i class="fas fa-eye"></i></button>
                  {{--                 <button
                  class="rounded bg-orange-400 px-2 py-1 font-bold text-white hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-300"
                  wire:click="addInventory({{ $product->id }})"><i class="fas fa-cart-plus"></i></button> --}}
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($products as $product)
          <div class="overflow-hidden rounded-lg bg-white shadow-lg">
            <div class="p-6">
              <h3 class="mb-2 text-lg font-semibold text-gray-900">
                {{ $product->name }}
              </h3>
              <span class="block text-sm text-gray-600">
                Categoría: {{ $product->category ? $product->category->name : 'Sin Asignar' }}
              </span>
              <p class="block text-sm text-gray-600">
                Estado:
                <span
                  class="{{ $product->state == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                  {{ $product->state == 1 ? 'Disponible' : 'Agotado' }}
                </span>
              </p>
              <div class="mt-2 text-right">
                <button
                  class="rounded bg-yellow-400 px-2 py-1 font-bold text-white hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                  wire:click="edit({{ $product->id }})"><i class="fas fa-edit"></i></button>
                <button
                  class="rounded bg-red-400 px-2 py-1 font-bold text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300"
                  wire:click="alertDelete({{ $product->id }})"><i class="fas fa-trash"></i></button>
                <button
                  class="rounded bg-cyan-400 px-2 py-1 font-bold text-white hover:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300"
                  wire:click="view({{ $product->id }})"><i class="fas fa-eye"></i></button>
                {{--                 <button
                  class="rounded bg-orange-400 px-2 py-1 font-bold text-white hover:bg-orange-500 focus:outline-none focus:ring-2 focus:ring-orange-300"
                  wire:click="addInventory({{ $product->id }})"><i class="fas fa-cart-plus"></i></button> --}}
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
    <div class="mt-4">
      {{ $products->links() }}
    </div>
    <x-modal maxWidth="lg" name="{{ $modalCreateOrUpdate }}" :show="false" focusable>
      <form wire:submit.prevent="save" class="p-6">
        <div class="text-lg font-medium text-gray-900">{{ $form->id ? 'Editar Producto' : 'Crear Producto' }}</div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Nombre</label>
          <input type="text" wire:model="form.name" class="form-input mt-1 block w-full rounded-md shadow-sm">
          @error('form.name')
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
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Categoría</label>
          <select wire:model="form.category_id" class="form-select mt-1 block w-full rounded-md shadow-sm">
            <option>Seleccionar Categoría</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
          </select>
          @error('form.category_id')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Estado</label>
          <select wire:model="form.state" class="form-select mt-1 block w-full rounded-md shadow-sm">
            <option value="1">Disponible</option>
            <option value="2">Agotado</option>
          </select>
          @error('form.state')
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
