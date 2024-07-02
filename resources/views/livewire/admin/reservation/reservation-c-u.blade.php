{{-- Contenido --}}
<div x-data="{ open: false, showVariations: false, openAreas: false }">
  <div class="mb-4 flex justify-between">
    <div class="flex items-center gap-2">
      <button @click="open = !open" class="flex items-center rounded bg-blue-500 px-4 py-2 text-xs text-white">
        <i class="fas" :class="open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        <span class="ml-2">Reserva</span>
      </button>
{{--       <button class="flex items-center rounded bg-purple-500 px-4 py-2 text-xs text-white">
        <i class="fas fa-broom"></i>
        <span class="ml-2">Limpiar</span>
      </button>
      <button wire:click="recargar_precios" class="flex items-center rounded bg-cyan-500 px-4 py-2 text-xs text-white">
        <i class="fas fa-broom"></i>
        <span class="ml-2">Precios</span>
      </button> --}}
      <button wire:click="save"
        class="flex items-center rounded bg-green-500 px-4 py-2 text-xs text-white hover:bg-green-700 focus:outline-none focus:ring-4 focus:ring-green-300">
        <i class="fas fa-plus"></i>
        <span class="ml-2">Guardar</span>
      </button>
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
    <div class="flex space-x-2">
      <button @click="openAreas = !openAreas"
        class="flex items-center rounded bg-orange-500 px-4 py-2 text-xs text-white">
        <i class="fas" :class="openAreas ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
        <span class="ml-2">Areas</span>
      </button>
    </div>
  </div>
  <div x-show="openAreas"
    class="mb-4 flex justify-between rounded-lg bg-slate-100 shadow-lg transition-all duration-300">
    <div class="flex space-x-4 overflow-x-auto whitespace-nowrap px-4 py-2">
      @forelse ($areas as $area)
        <button wire:click="selectArea({{ $area->id }})"
          class="flex items-center rounded-lg px-4 py-2 text-sm font-medium uppercase text-slate-700 hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-slate-300">
          <i class="fas fa-utensils text-slate-500"></i>
          <span class="ml-2">{{ $area->name }}</span>
        </button>
      @empty
        <p class="text-sm text-gray-500">No hay areas registradas</p>
      @endforelse
    </div>
  </div>
  <div class="flex flex-col lg:flex-row lg:space-x-4">
    <!-- Left Section: Selected Products and Search -->
    <div class="w-full lg:w-1/3">
      <!-- Buffet General Data Form -->
      <div x-show="open" class="mb-4 rounded bg-white p-4 shadow-md">
        <h2 class="mb-4 font-bold">Datos Generales del Buffet</h2>
        <form class="flex flex-wrap text-xs">
          <div class="w-full p-2">
            <label class="mb-1 block font-bold">Empresa</label>
            <div x-data="{ open: false }" class="relative">
              <div class="flex items-center">
                <input type="text" wire:model.live="companySearch" @focus="open = true"
                  @blur="setTimeout(() => open = false, 200)"
                  class="w-full rounded-l border border-r-0 border-gray-300 p-2 text-xs"
                  placeholder="Buscar empresa...">
                <button wire:click.prevent="openModal('{{ $modalCreateCompany }}')"
                  class="flex h-9 w-10 items-center justify-center rounded-r bg-blue-500 text-white hover:bg-blue-600">
                  <i class="fas fa-building"></i>
                </button>
              </div>
              @if ($companySearch)
                <div class="relative w-full">
                  <ul
                    class="absolute z-20 mt-1 max-h-60 w-full overflow-auto rounded-md border border-gray-300 bg-white shadow-lg"
                    x-show="open">
                    @forelse ($searchResults as $company)
                      <li wire:click="selectCompany({{ $company->id }}, '{{ $company->name }}')"
                        class="cursor-pointer px-4 py-2 hover:bg-gray-200">
                        {{ $company->name }}
                      </li>
                    @empty
                      <li class="px-4 py-2 text-gray-500">No se encontró la empresa (registrar)</li>
                    @endforelse
                  </ul>
                </div>
              @endif
            </div>
          </div>
          <div class="w-full p-2">
            @foreach ($selectedCompanies as $index => $company)
              <div class="mb-4 rounded-lg border bg-gray-50 p-4">
                <div class="mb-2 flex items-center justify-between">
                  <h3 class="text-sm font-bold">{{ $company['name'] }}</h3>
                  <button wire:click.prevent="removeCompany({{ $index }})"
                    class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
                <div class="flex flex-col md:flex-row md:space-x-4">
                  <div class="mb-2 flex-1 md:mb-0">
                    <label class="mb-1 block text-xs font-bold">Pack</label>
                    <input type="number" wire:model.live="selectedCompanies.{{ $index }}.pack"
                      class="w-full rounded border border-gray-300 p-2 text-xs">
                  </div>
                  <div class="mb-2 flex-1 md:mb-0">
                    <label class="mb-1 block text-xs font-bold">Costo por Pack</label>
                    <input type="number" wire:model.live="selectedCompanies.{{ $index }}.cost_pack"
                      class="w-full rounded border border-gray-300 p-2 text-xs">
                  </div>
                  <div class="flex-1">
                    <label class="mb-1 block text-xs font-bold">Total Pack</label>
                    <input readonly disabled type="number"
                      wire:model="selectedCompanies.{{ $index }}.total_pack"
                      class="w-full cursor-not-allowed rounded border border-gray-300 p-2 text-xs opacity-70" readonly>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          <div class="w-full p-2 md:w-1/2">
            <label class="mb-1 block font-bold">Pack</label>
            <input readonly disabled wire:model.live="form.people_count" type="number"
              placeholder="Ingrese la cantidad de personas"
              class="w-full cursor-not-allowed rounded border border-gray-300 p-2 text-xs opacity-70">
          </div>
          <div class="w-full p-2 md:w-1/2">
            <label class="mb-1 block font-bold">Costo por Pack</label>
            <input readonly disabled wire:model.live="form.cost_pack" type="number"
              placeholder="Ingrese la cantidad de personas"
              class="w-full cursor-not-allowed rounded border border-gray-300 p-2 text-xs opacity-70">
          </div>
          <div class="w-full p-2 md:w-1/2">
            <label class="mb-1 block font-bold">Total Pack</label>
            <input wire:model="form.total_pack" type="number" placeholder="Ingrese la cantidad de personas"
              class="w-full cursor-not-allowed rounded border border-gray-300 p-2 text-xs opacity-70" readonly
              disabled>
          </div>
          <div class="w-full p-2 md:w-1/2">
            <label class="mb-1 block font-bold">Costo Total</label>
            <input wire:model="form.total_cost" type="number"
              class="w-full cursor-not-allowed rounded border border-gray-300 p-2 text-xs opacity-70" readonly
              disabled>
          </div>
          <div class="w-full p-2 md:w-1/2">
            <label class="mb-1 block font-bold">Total de Productos</label>
            <input wire:model="form.total_products" type="number"
              class="w-full cursor-not-allowed rounded border border-gray-300 p-2 text-xs opacity-70" readonly
              disabled>
          </div>
          <div class="w-full p-2 md:w-1/2">
            <label class="mb-1 block font-bold">Fecha del Pedido</label>
            <input wire:model="form.order_date" type="datetime-local"
              class="w-full rounded border border-gray-300 p-2 text-xs">
          </div>
          <div class="w-full p-2 md:w-1/2">
            <label class="mb-1 block font-bold">Fecha de Ejecución</label>
            <input wire:model="form.execution_date" type="datetime-local"
              class="w-full rounded border border-gray-300 p-2 text-xs">
          </div>
          <div class="w-full p-2 md:w-1/2">
            <label class="mb-1 block font-bold">Estado</label>
            <select wire:model="form.status" class="w-full rounded border border-gray-300 p-2 text-xs">
              <option value="1">Realizado</option>
              <option value="2">En Ejecución</option>
              <option value="3">Pendiente</option>
              <option value="4">Pospuesto</option>
            </select>
          </div>
          <div class="w-full p-2 md:w-1/2">
            <label class="mb-1 block font-bold">Estado de Pago</label>
            <select wire:model="form.payment_status" class="w-full rounded border border-gray-300 p-2 text-xs">
              <option value="1">Pagado</option>
              <option value="2">Pendiente</option>
            </select>
          </div>
          <div class="w-full p-2">
            <label class="mb-1 block font-bold">Comentarios</label>
            <textarea wire:model="form.description" class="w-full rounded border border-gray-300 p-2 text-xs"></textarea>
          </div>
        </form>
      </div>
      <!-- Selected Product List as Table -->
      <div class="rounded bg-white p-4 shadow-md">
        <h2 class="mb-4 font-bold">Productos Disponibles</h2>
        <input type="text" placeholder="Buscar en productos seleccionados..." wire:model.live="form.searchP"
          class="mb-4 w-full rounded border border-gray-300 p-2 text-xs">
        <div class="overflow-x-auto">
          <table class="min-w-full bg-white text-xs" x-show="!showVariations">
            <thead>
              <tr>
                <th class="select-none bg-gray-200 px-4 py-2 text-center">Producto</th>
                <th class="select-none bg-gray-200 px-4 py-2 text-center">Unidades</th>
              </tr>
            </thead>
            <tbody>
            <tbody>
              @foreach ($products as $product)
                @if ($product)
                  <tr wire:click="showVariations({{ $product->id }})" @click="showVariations = true"
                    class="cursor-pointer hover:bg-gray-100">
                    <td class="select-none border-b px-4 py-2 text-center">{{ $product->name }}</td>
                    <td class="select-none border-b px-4 py-2 text-center">
                      <div class="flex select-none items-center justify-center space-x-2">
                        <span>{{ $product->variations()->count() }}</span>
                      </div>
                    </td>
                  </tr>
                @endif
              @endforeach
            </tbody>
            </tbody>
          </table>
          <!-- Variations Table -->
          <table class="min-w-full bg-white text-xs" x-show="showVariations" wire:loading.class="hidden">
            <thead>
              <tr>
                <th class="select-none bg-gray-200 px-4 py-2 text-center">Unidad</th>
                <th class="select-none bg-gray-200 px-4 py-2 text-center">Stock</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($variations as $variation)
                <tr class="cursor-pointer hover:bg-gray-100" wire:key="{{ $variation->id }}"
                  wire:click="selectVariation({{ $variation->id }}, 'create')" @click="showVariations = false">
                  <td class="select-none border-b px-4 py-2 text-center">{{ $variation->unit->abbreviation }}</td>
                  <td class="select-none border-b px-4 py-2 text-center">
                    <span>{{ $variation->quantity_base }}</span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <div x-show="showVariations" class="mt-2 flex justify-center" wire:loading.class="hidden">
            <span @click="showVariations = false"
              class="cursor-pointer text-blue-500 underline hover:text-blue-700">Atrás</span>
          </div>
        </div>
      </div>
    </div>
    <div class="w-full lg:w-2/3">
      <div class="rounded bg-white p-4 shadow-md">
        <div>
          @if (!empty($form->productsExceedingStock))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => { $wire.clearErrorStock() }, 5000)"
              @clear-error-stock.window="show = false" id="alert"
              class="relative mb-4 rounded border border-red-400 bg-red-100 px-4 py-3 text-red-700" role="alert">
              <strong class="font-bold">Error:</strong><br>
              <ul class="mt-2 list-inside list-disc">
                @foreach ($form->productsExceedingStock as $product)
                  <li>
                    Excede el {{ $product['product_name'] }} por {{ $product['exceeded_quantity'] }} de cantidad.
                  </li>
                @endforeach
              </ul>
              <div class="absolute bottom-0 left-0 h-1 animate-progress bg-red-500"></div>
              <span class="absolute bottom-0 right-0 top-0 px-4 py-3">
                <i class="fas fa-times cursor-pointer text-red-500"
                  @click="show = false; Livewire.emit('clearErrorStock')"></i>
              </span>
            </div>
          @endif
        </div>
        <h2 class="mb-4 font-bold">Productos Seleccionados</h2>
        <input type="text" wire:model.live="form.searchS" placeholder="Buscar en productos seleccionados..."
          class="mb-4 w-full rounded border border-gray-300 p-2 text-xs">
        <div class="overflow-x-auto">
          <table class="min-w-full bg-white text-xs">
            <thead>
              <tr>
                <th class="bg-gray-200 px-4 py-2 text-center">Producto</th>
                <th class="bg-gray-200 px-4 py-2 text-center">Precio</th>
                <th class="bg-gray-200 px-4 py-2 text-center">Cantidad</th>
                <th class="bg-gray-200 px-4 py-2 text-center">Stock</th>
                <th class="bg-gray-200 px-4 py-2 text-center">Stock Inicial</th>
                <th class="bg-gray-200 px-4 py-2 text-center">Unidad de Medida</th>
                <th class="bg-gray-200 px-4 py-2 text-center">Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse ($selectedProducts as $index => $product)
                <tr wire:key="product-{{ $product['index'] }}">
                  <td class="border-b px-4 py-2 text-center">{{ $product['product_name'] }}</td>
                  <td class="border-b px-4 py-2 text-center">
                    <input wire:model="form.selectedProducts.{{ $product['index'] }}.variation_price"
                      {{ $product['price_edit'] ? 'disabled' : '' }}
                      class="w-16 rounded border border-gray-300 text-center text-xs" />
                  </td>
                  <td class="border-b px-4 py-2 text-center">
                    <div class="flex items-center justify-center space-x-2">
                      <button wire:loading.class="opacity-50" wire:loading.attr="disabled"
                        wire:click="decrementQuantity({{ $product['variation_id'] }})"
                        class="flex h-7 w-7 items-center justify-center rounded-full border border-red-500 text-red-500 shadow-md transition duration-200 hover:bg-red-500 hover:text-white">
                        <i class="fas fa-minus w-4"></i>
                      </button>
                      <input wire:model.defer="form.selectedProducts.{{ $product['index'] }}.quantity"
                        {{ $product['quantity_edit'] ? 'disabled' : '' }}
                        class="w-14 rounded border border-gray-300 text-center text-xs"
                        value="{{ $product['quantity'] }}" />
                      <button wire:loading.class="opacity-50" wire:loading.attr="disabled"
                        wire:click="incrementQuantity({{ $product['variation_id'] }})"
                        class="flex h-7 w-7 items-center justify-center rounded-full border border-green-500 text-green-500 shadow-md transition duration-200 hover:bg-green-500 hover:text-white">
                        <i class="fas fa-plus w-4"></i>
                      </button>
                    </div>
                  </td>
                  <td class="border-b px-4 py-2 text-center">{{ $product['variation_stock'] }}</td>
                  <td class="border-b px-4 py-2 text-center">{{ $product['initial_stock'] }}</td>
                  <td class="border-b px-4 py-2 text-center">{{ $product['unit_abbreviation'] }}</td>
                  <td class="border-b px-4 py-2 text-center">
                    <button
                      class="{{ $product['quantity_edit'] && $product['price_edit'] ? 'bg-yellow-400 hover:bg-yellow-500 focus:ring-yellow-300' : 'bg-green-400 hover:bg-green-500 focus:ring-green-300' }} h-7 w-7 rounded px-2 py-1 font-bold text-white focus:outline-none focus:ring-2"
                      wire:click="selectVariation({{ $product['variation_id'] }}, 'edit')">
                      <i
                        class="fas {{ $product['quantity_edit'] && $product['price_edit'] ? 'fa-edit' : 'fa-check' }}"></i>
                    </button>
                    <button
                      class="h-7 w-7 rounded bg-red-400 px-2 py-1 font-bold text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300"
                      wire:click="selectVariation({{ $product['variation_id'] }}, 'delete')">
                      <i class="fas fa-trash"></i>
                    </button>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="border-b px-4 py-2 text-center">
                    <p class="text-sm text-gray-500">No hay productos seleccionados</p>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-5">
          {{ $selectedProducts->links() }}
        </div>
      </div>
    </div>
  </div>
  <x-modal maxWidth="sm" name="{{ $modalCreateCompany }}" :show="false" focusable>
    <form wire:submit.prevent="saveCompany" class="p-6">
      <div class="text-lg font-medium text-gray-900">Crear Empresa</div>
      <div class="mt-4">
        <label class="block text-sm font-medium text-gray-700">Empresa</label>
        <input type="text" wire:model="name" class="form-input mt-1 block w-full rounded-md shadow-sm">
        @error('name')
          <span class="text-sm text-red-500">{{ $message }}</span>
        @enderror
      </div>
      <div class="mt-6 flex justify-end">
        <x-secondary-button wire:click="closeModal('{{ $modalCreateCompany }}')">
          {{ __('Cancelar') }}
        </x-secondary-button>
        <x-primary-button class="ms-3" wire:loading.class="opacity-50" wire:loading.attr="disabled">
          {{ __('Guardar') }}
        </x-primary-button>
      </div>
    </form>
  </x-modal>
</div>
