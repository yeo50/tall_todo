<?php

use App\Models\Task;
use App\Models\Catalogue;
use Livewire\Volt\Component;

new class extends Component {
    public $catalogues;

    public $name;
    public function mount()
    {
        $this->catalogues = Catalogue::all();
    }
    public function addCatalogue()
    {
        $catalogue = new Catalogue(['user_id' => auth()->id(), 'name' => $this->name]);
        $catalogue->save();
        $this->catalogues->push($catalogue);
    }
};
?>
<div class="flex flex-col">
    @foreach ($catalogues as $item)
        @php
            $routeParam = request()->route('catalogue') ?? request()->route('id');
            $isActive = request()->routeIs('catalogues.show') && $routeParam->id == $item->id;
        @endphp

        <a href="{{ route('catalogues.show', $item->id) }}" @class([
            'py-2 px-3 w-full capitalize relative before:absolute  before:left-0  before:-mt-2 before:h-0 before:bg-blue-500 before:w-1 ',
            'bg-gray-200 dark:bg-gray-300/10  before:animate-stand before:h-full' => $isActive,
        ])>
            {{ $item->name }}
        </a>
    @endforeach
    <div x-data="{ nameInput: '' }" class="flex items-center justify-between gap-x-1 py-2 px-3  cursor-pointer">
        <div class="flex items-center">
            <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="h-6 w-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
            </span>
            <input type="text" wire:model="name" x-model="nameInput" placeholder="Add Catalogue"
                class="placeholder:text-blue-700 dark:bg-gray-900 border-0 min-w-32  w-28 flex-1 focus:ring-0">
        </div>
        <button @click="$wire.addCatalogue(); nameInput = ''" :disabled="!nameInput.trim()"
            :class="!nameInput.trim() ? 'hidden' : ''" wire:loading.attr="disabled"
            class="p-2 rounded-lg shadow-lg font-semibold text-black  bg-white disabled:bg-gray-200 dark:text-white dark:bg-gray-800 cursor-pointer ">
            Save</button>
    </div>
</div>
