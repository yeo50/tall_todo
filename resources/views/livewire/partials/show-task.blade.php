<?php

use App\Models\Task;
use App\Models\Catalogue;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public function softDelete()
    {
        $task = Task::find($id);
        $task->delete();
        $this->dispatch('soft-deleted');
    }
};

?>

<div>
    <div
        class="fixed top-[66px] shadow-lg right-0  overflow-y-scroll bg-white text-black w-96 max-h-[calc(100vh-112px)] h-[calc(100vh-112px)]">
        <div class="w-full h-full">

            <template x-if="selectedTask.deleted_at == null">
                <div
                    class="py-2 my-1 px-3 w-full rounded-lg shadow-sm cursor-pointer bg-white text-black  text-sm flex items-center gap-x-3">
                    <span @click="$wire.softDelete(selectedTask.id)"
                        class="w-4 h-4 shrink-0 leading-[13px] text-center text-black/0 text-xs hover:text-blue-700 border cursor-pointer border-blue-600 inline-block rounded-full ">&check;
                    </span>
                    <span x-text="selectedTask ? selectedTask.name : 'no task selected'"> </span>
                </div>
            </template>
            <template x-if="selectedTask.deleted_at != null">
                <div class="h-10 cursor-pointer bg-white px-3 py-2 flex items-center my-1.5 shadow-sm rounded-lg">
                    <span
                        class="w-4 h-4 shrink-0 leading-[13px] text-center  text-xs bg-blue-700  text-white inline-block rounded-full ">&check;
                    </span>
                    <span x-text="selectedTask.name" class="line-through text-gray-800 font-semibold ps-3">

                    </span>
                </div>
            </template>

            <div x-data class="h-full">
                <ul>

                    <template x-for="i in 2">
                        <li x-text="i"></li>
                    </template>
                </ul>
            </div>
        </div>

    </div>
    <div class="fixed right-0 bottom-0 bg-white w-96 h-12 ">
        <div class="flex justify-between items-center px-2 py-2 dark:text-black">
            <span @click="showTask = false"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="h-5 w-5 cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                </svg>
            </span>
            <span class="font-semibold text-gray-800">Created Today</span>
            <span><svg @click="deleteConfirm = true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
            </span>
        </div>
    </div>
</div>
