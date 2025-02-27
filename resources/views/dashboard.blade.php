<x-app-layout>

    <x-slot name="pageTitleBar">{{ $catalogue->name }} > lists</x-slot>
    <livewire:partials.add-task catalogueName="{{ $catalogue->name }}" />

</x-app-layout>
