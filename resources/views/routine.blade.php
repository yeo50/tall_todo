<x-app-layout>
    {{-- <livewire:partials.testing-flatpickr /> --}}


    <input type="date" id="datePicker">
    <input type="time" id="timePicker">
    <input type="datetime-local" id="dateTimePicer">
    <button onclick="document.getElementById('datePicker').showPicker()">Show Date Picker</button>
    <button onclick="document.getElementById('timePicker').showPicker()">Show Time Picker</button>
    <button onclick="document.getElementById('dateTimePicer').showPicker()">Show DateTime Picker</button>


</x-app-layout>
