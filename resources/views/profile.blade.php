<x-app-layout>
  {{-- Encabezado --}}
  <x-slot name="header">
    {{ __('Perfil') }}
  </x-slot>

  {{-- Contenido --}}
  <div class="flex max-w-xl flex-col space-y-12">
    <livewire:profile.update-profile-information-form />
    <livewire:profile.update-password-form />
  </div>
</x-app-layout>
