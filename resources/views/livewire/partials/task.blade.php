<?php

use App\Models\Task;
use App\Models\Catalogue;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $task;
    public function softDelete($id)
    {
        $task = Task::find($id);
        $task->delete();
        $this->dispatch('soft-deleted');
    }
};
?>
<div
    class="py-2 my-1 px-3 w-full rounded-lg shadow-sm cursor-pointer bg-white text-black  text-sm flex items-center gap-x-3">
    <span wire:click="softDelete( {{ $task->id }} )"
        class="w-4 h-4 shrink-0 leading-[13px] text-center text-black/0 text-xs hover:text-blue-700 border cursor-pointer border-blue-600 inline-block rounded-full ">&check;
    </span>
    <span @click="showTask = true;$wire.dispatch('select',{id :{{ $task->id }}})" class="flex-1">{{ $task->name }}
    </span>

</div>
