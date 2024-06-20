@props(['active', 'label', 'links'])

@php
  $buttonClasses =
      $active ?? false
          ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
          : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<div x-data="{ open: false }" class="relative" @click.away="open = false">
  <button @click="open = !open" class="{{ $buttonClasses }} h-full">
    {{ $label }}
    <div class="ms-1">
      <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path fill-rule="evenodd"
          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
          clip-rule="evenodd" />
      </svg>
    </div>
  </button>
  <div x-show="open" x-transition:enter="transition ease-out duration-100"
    x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
    x-transition:leave-end="transform opacity-0 scale-95"
    class="absolute left-0 z-50 mt-0 w-48 rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
    <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
      @foreach ($links as $link)
        @php
          $isActive = false;
          foreach ($link['activeRoutes'] as $route) {
              if (request()->routeIs($route)) {
                  $isActive = true;
                  break;
              }
          }
          $linkClasses = $isActive
              ? 'block px-4 py-2 text-sm text-white bg-indigo-600'
              : 'block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100';
        @endphp
        <a href="{{ $link['href'] }}" class="{{ $linkClasses }}" role="menuitem" wire:navigate>
          {{ $link['text'] }}
        </a>
      @endforeach
    </div>
  </div>
</div>