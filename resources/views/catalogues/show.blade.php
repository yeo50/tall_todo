<x-app-layout>

    <x-slot name="pageTitleBar">{{ $catalogue?->name }} > lists</x-slot>
    <livewire:partials.add-task catalogueId="{{ $catalogue?->id }}" />
</x-app-layout>
