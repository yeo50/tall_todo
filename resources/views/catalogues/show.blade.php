<x-app-layout>
    <x-slot name="pageTitleBar">{{ $catalogue->name }} > lists</x-slot>
    @session('message')
        <p x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show" class="my-4 text-xl text-center text-green-800">
            {{ session('message') }}</p>
    @endsession

    <livewire:partials.add-task catalogueName="{{ $catalogue->name }}" />
</x-app-layout>
