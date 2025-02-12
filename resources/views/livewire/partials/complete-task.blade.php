<?php

use Livewire\Volt\Component;

new class extends Component {
    public $item;
};
?>

<div @click="showTask = true; selectedTask = {{ json_encode($item) }}"
    class="h-10 cursor-pointer bg-white px-3 py-2 flex items-center my-1.5 shadow-sm rounded-lg">
    <span
        class="w-4 h-4 shrink-0 leading-[13px] text-center  text-xs bg-blue-700  text-white inline-block rounded-full ">&check;
    </span>
    <span class="line-through text-gray-800 font-semibold ps-3">
        {{ $item->name }}
    </span>
</div>
