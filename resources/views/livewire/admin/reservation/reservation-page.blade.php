<div>
  <x-slot name="header">
    {{ __('Reservas') }}
  </x-slot>

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
        <div class="flex w-full justify-center">
          <svg wire:loading class="animate-spin text-gray-300" viewBox="0 0 64 64" fill="none"
            xmlns="http://www.w3.org/2000/svg" width="24" height="24">
            <path
              d="M32 3C35.8083 3 39.5794 3.75011 43.0978 5.20749C46.6163 6.66488 49.8132 8.80101 52.5061 11.4939C55.199 14.1868 57.3351 17.3837 58.7925 20.9022C60.2499 24.4206 61 28.1917 61 32C61 35.8083 60.2499 39.5794 58.7925 43.0978C57.3351 46.6163 55.199 49.8132 52.5061 52.5061C49.8132 55.199 46.6163 57.3351 43.0978 58.7925C39.5794 60.2499 35.8083 61 32 61C28.1917 61 24.4206 60.2499 20.9022 58.7925C17.3837 57.3351 14.1868 55.199 11.4939 52.5061C8.801 49.8132 6.66487 46.6163 5.20749 43.0978C3.7501 39.5794 3 35.8083 3 32C3 28.1917 3.75011 24.4206 5.2075 20.9022C6.66489 17.3837 8.80101 14.1868 11.4939 11.4939C14.1868 8.80099 17.3838 6.66487 20.9022 5.20749C24.4206 3.7501 28.1917 3 32 3L32 3Z"
              stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"></path>
            <path
              d="M32 3C36.5778 3 41.0906 4.08374 45.1692 6.16256C49.2477 8.24138 52.7762 11.2562 55.466 14.9605C58.1558 18.6647 59.9304 22.9531 60.6448 27.4748C61.3591 31.9965 60.9928 36.6232 59.5759 40.9762"
              stroke="currentColor" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"
              class="text-gray-900">
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
          wire:click="formCU()">
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
              Fecha de Pedido</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Fecha de Ejecución</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Total Pago</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Número de Personas</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Total de Productos</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Estado</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($reservations as $reservation)
            <tr>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">{{ $reservation->id }}
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                <span class="block">{{ $reservation->order_date->format('d/m/Y') }}</span>
                <span class="text-xs font-bold">({{ $reservation->order_date->format('H:i') }})</span>
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                <span
                  class="block">{{ $reservation->execution_date ? $reservation->execution_date->format('d/m/Y') : 'N/A' }}</span>
                <span
                  class="text-xs font-bold">({{ $reservation->execution_date ? $reservation->execution_date->format('H:i') : 'N/A' }})</span>
              </td>

              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                S/. {{ number_format($reservation->total_cost, 2) }}</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $reservation->people_count }} (pack)</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $reservation->total_products }} productos</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                <span
                  class="{{ $reservation->status == 1 ? 'bg-green-100 text-green-800' : '' }} {{ $reservation->status == 2 ? 'bg-blue-100 text-blue-800' : '' }} {{ $reservation->status == 3 ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $reservation->status == 5 ? 'bg-red-100 text-red-800' : '' }} {{ $reservation->status == 4 ? 'bg-purple-100 text-purple-800' : '' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                  @switch($reservation->status)
                    @case(1)
                      En Proceso
                    @break

                    @case(2)
                      En Ejecución
                    @break

                    @case(3)
                      Pendiente
                    @break

                    @case(4)
                      Pospuesto
                    @break

                    @case(5)
                      Cancelado
                    @break
                  @endswitch
                </span>
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center">
                <div class="flex gap-1">
                  <button
                    class="rounded bg-yellow-400 px-2 py-1 font-bold text-white hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                    wire:click="formCU({{ $reservation->id }})"><i class="fas fa-edit"></i></button>
                  <button
                    class="rounded bg-red-400 px-2 py-1 font-bold text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300"
                    wire:click="alertDelete({{ $reservation->id }})"><i class="fas fa-trash"></i></button>
                  <button
                    class="rounded bg-cyan-400 px-2 py-1 font-bold text-white hover:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300"
                    wire:click="view({{ $reservation->id }})"><i class="fas fa-eye"></i></button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @else
      <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($reservations as $reservation)
          <div class="overflow-hidden rounded-lg bg-white shadow-lg">
            <div class="p-6">
              <h3 class="mb-2 text-lg font-semibold text-gray-900">
                {{ $reservation->company_name }}
              </h3>
              <span class="block text-sm text-gray-600">
                Estado: <span
                  class="{{ $reservation->status == 1 ? 'bg-green-100 text-green-800' : '' }} {{ $reservation->status == 2 ? 'bg-blue-100 text-blue-800' : '' }} {{ $reservation->status == 3 ? 'bg-yellow-100 text-yellow-800' : '' }} {{ $reservation->status == 4 ? 'bg-red-100 text-red-800' : '' }} {{ $reservation->status == 5 ? 'bg-purple-100 text-purple-800' : '' }} inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold leading-5">
                  @switch($reservation->status)
                    @case(1)
                      Realizada
                    @break

                    @case(2)
                      En Ejecución
                    @break

                    @case(3)
                      Pendiente
                    @break

                    @case(4)
                      Cancelada
                    @break

                    @case(5)
                      Pospuesta
                    @break
                  @endswitch
                </span>
              </span>

              </span>
              <span class="block text-sm text-gray-600">
                Estado de Pago: <span
                  class="{{ $reservation->payment_status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                  {{ $reservation->payment_status == 1 ? 'Pagado' : 'Pendiente de Pago' }}
                </span>
              </span>
              <span class="block text-sm text-gray-600">
                Fecha de Pedido: {{ $reservation->order_date->format('d/m/Y H:i') }}
              </span>
              <span class="block text-sm text-gray-600">
                Fecha de Ejecución:
                {{ $reservation->execution_date ? $reservation->execution_date->format('d/m/Y H:i') : 'N/A' }}
              </span>
              <span class="block text-sm text-gray-600">
                Costo Total: {{ number_format($reservation->total_cost, 2) }}
              </span>
              <span class="block text-sm text-gray-600">
                Número de Personas: {{ $reservation->people_count }}
              </span>
              <span class="block text-sm text-gray-600">
                Total de Productos: {{ $reservation->total_products }}
              </span>
              <div class="mt-2 text-right">
                <button
                  class="rounded bg-yellow-400 px-2 py-1 font-bold text-white hover:bg-yellow-500 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                  wire:click="edit({{ $reservation->id }})"><i class="fas fa-edit"></i></button>
                <button
                  class="rounded bg-red-400 px-2 py-1 font-bold text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-300"
                  wire:click="alertDelete({{ $reservation->id }})"><i class="fas fa-trash"></i></button>
                <button
                  class="rounded bg-cyan-400 px-2 py-1 font-bold text-white hover:bg-cyan-500 focus:outline-none focus:ring-2 focus:ring-cyan-300"
                  wire:click="view({{ $reservation->id }})"><i class="fas fa-eye"></i></button>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
    <div class="mt-4">
      {{ $reservations->links() }}
    </div>
    <x-modal maxWidth="lg" name="{{ $modalCreateOrUpdate }}" :show="false" focusable>
      <form wire:submit.prevent="save" class="p-6">
        <div class="text-lg font-medium text-gray-900">{{ $form->id ? 'Editar Reserva' : 'Crear Reserva' }}</div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Empresa</label>
          <input type="text" wire:model="form.company_name"
            class="form-input mt-1 block w-full rounded-md shadow-sm">
          @error('form.company_name')
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
          <label class="block text-sm font-medium text-gray-700">Estado</label>
          <select wire:model="form.status" class="form-select mt-1 block w-full rounded-md shadow-sm">
            <option value="1">Realizada</option>
            <option value="2">En Ejecución</option>
            <option value="3">Pendiente</option>
            <option value="4">Cancelada</option>
            <option value="5">Pospuesta</option>
          </select>
          @error('form.status')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Estado de Pago</label>
          <select wire:model="form.payment_status" class="form-select mt-1 block w-full rounded-md shadow-sm">
            <option value="1">Pagado</option>
            <option value="2">Pendiente de Pago</option>
          </select>
          @error('form.payment_status')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Tipo</label>
          <select wire:model="form.type" class="form-select mt-1 block w-full rounded-md shadow-sm">
            <option value="1">Normal</option>
            <option value="2">Otro</option>
          </select>
          @error('form.type')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Fecha de Pedido</label>
          <input type="datetime-local" wire:model="form.order_date"
            class="form-input mt-1 block w-full rounded-md shadow-sm">
          @error('form.order_date')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Fecha de Ejecución</label>
          <input type="datetime-local" wire:model="form.execution_date"
            class="form-input mt-1 block w-full rounded-md shadow-sm">
          @error('form.execution_date')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Costo Total</label>
          <input type="number" step="0.01" wire:model="form.total_cost"
            class="form-input mt-1 block w-full rounded-md shadow-sm">
          @error('form.total_cost')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Número de Personas</label>
          <input type="number" wire:model="form.people_count"
            class="form-input mt-1 block w-full rounded-md shadow-sm">
          @error('form.people_count')
            <span class="text-sm text-red-500">{{ $message }}</span>
          @enderror
        </div>
        <div class="mt-4">
          <label class="block text-sm font-medium text-gray-700">Total de Productos</label>
          <input type="number" wire:model="form.total_products"
            class="form-input mt-1 block w-full rounded-md shadow-sm">
          @error('form.total_products')
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
