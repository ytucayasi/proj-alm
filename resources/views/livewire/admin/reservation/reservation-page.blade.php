<div>
  <x-slot name="header">
    {{ __('Reservas (Falta)') }}
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
              Estado</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Estado de Pago</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Fecha de Pedido</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Fecha de Ejecución</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Costo Total</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Número de Personas</th>
            <th
              class="border-b-2 border-gray-200 bg-gray-100 px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-gray-600">
              Total de Productos</th>
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
                <span
                  class="{{ $reservation->status == 1 ? 'text-green-400' : '' }} {{ $reservation->status == 2 ? 'text-blue-400' : '' }} {{ $reservation->status == 3 ? 'text-yellow-400' : '' }} {{ $reservation->status == 4 ? 'text-red-400' : '' }} {{ $reservation->status == 5 ? 'text-purple-400' : '' }} text-lg font-semibold leading-5">
                  @switch($reservation->status)
                    @case(1)
                      <i class="fas fa-check-circle"></i> <!-- Realizada -->
                    @break

                    @case(2)
                      <i class="fas fa-play-circle"></i> <!-- En Ejecución -->
                    @break

                    @case(3)
                      <i class="fas fa-clock"></i> <!-- Pendiente -->
                    @break

                    @case(4)
                      <i class="fas fa-times-circle"></i> <!-- Cancelada -->
                    @break

                    @case(5)
                      <i class="fas fa-pause-circle"></i> <!-- Pospuesta -->
                    @break
                  @endswitch
                </span>

              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                <span
                  class="{{ $reservation->payment_status == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} inline-flex rounded-full px-2 text-xs font-semibold leading-5">
                  {{ $reservation->payment_status == 1 ? 'Pagado' : 'Pendiente' }}
                </span>
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                <span class="block">{{ $reservation->order_date->format('d/m/Y') }}</span>
                <span>({{ $reservation->order_date->format('H:i') }})</span>
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                <span class="block">{{ $reservation->execution_date ? $reservation->execution_date->format('d/m/Y') : 'N/A' }}</span>
                <span>({{ $reservation->execution_date ? $reservation->execution_date->format('H:i') : 'N/A' }})</span>
              </td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ number_format($reservation->total_cost, 2) }}</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $reservation->people_count }}</td>
              <td class="border-b border-gray-200 px-5 py-2 text-center text-sm text-gray-900">
                {{ $reservation->total_products }}</td>
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
