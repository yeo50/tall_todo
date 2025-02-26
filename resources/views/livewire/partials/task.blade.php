<?php

use App\Models\Task;
use Livewire\Volt\Component;

new class extends Component {
    public $task;
    public $selectedTaskId;

    public function softDelete($id)
    {
        $task = Task::find($id);
        $task->delete();
        $this->dispatch('soft-deleted', $id);
    }
};
?>
<div @class([
    'py-2 my-1 px-3 w-full  rounded-lg shadow-sm cursor-pointer  text-black hover:bg-gray-200 dark:hover:bg-gray-700   text-sm flex items-center gap-x-3',
    'bg-gray-300 dark:bg-gray-700 dark:text-white' =>
        $task->id == $selectedTaskId,
    'bg-white dark:bg-gray-800 dark:text-white' => $task->id != $selectedTaskId,
])>
    <span wire:click="softDelete( {{ $task->id }} )"
        class="w-4 h-4 shrink-0 leading-[13px] text-center text-black/0 text-xs hover:text-blue-700 border cursor-pointer border-blue-600 inline-block rounded-full ">&check;
    </span>
    <span @click="$wire.dispatch('select',{id :{{ $task->id }}});showTask = true;$dispatch('remove-profile')"
        class="flex-1">{{ $task->name }}
    </span>

</div>
