<?php

use App\Models\Task;
use App\Models\Catalogue;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

new class extends Component {
    #[Validate('required')]
    public $name;
    public $catalogueName;
    public $catalogue;
    public $tasks;
    public $model;
    public $date;
    public $reminder;
    public $completedTasks;
    public $completedCount;
    public $selectedTask;

    public function mount()
    {
        $this->catalogue = Catalogue::where('name', $this->catalogueName)->first();
        $this->tasks = $this->catalogue->tasks;
        $this->completedTasks = $this->catalogue->tasks()->onlyTrashed()->get();
        $this->completedCount = $this->completedTasks->count();
        $this->showTask = false;
        $this->selectedTask = null;
    }
    public function setDue($dateString)
    {
        if ($dateString == 'today') {
            $this->date = Carbon::today()->toDateString();
        }
        if ($dateString == 'tomorrow') {
            $this->date = Carbon::tomorrow()->toDateString();
        }
        if ($dateString == 'next_week') {
            $this->date = Carbon::now()->addWeek()->toDateString();
        }
    }
    public function setReminder($dateString)
    {
        if ($dateString == 'today') {
            $this->reminder = Carbon::today()->toDateString() . ' ' . '23:00';
        }
        if ($dateString == 'tomorrow') {
            $this->reminder = Carbon::tomorrow()->toDateString();
        }
        if ($dateString == 'next_week') {
            $this->reminder = Carbon::now()->addWeek()->toDateString();
        }
    }
    public function submit()
    {
        $this->validate();
        $user_id = Auth::id();

        $task = new Task(['user_id' => $user_id, 'catalogue_id' => $this->catalogue->id, 'name' => $this->name]);
        if ($this->date) {
            $task->due = $this->date;
        }
        if ($this->reminder) {
            $task->reminder = $this->reminder;
        }
        $this->catalogue->tasks()->save($task);
        $this->catalogue->refresh();
        $this->name = '';
        $this->date = '';
        $this->reminder = '';
        $this->tasks = $this->catalogue->tasks;
    }
    #[On('soft-deleted')]
    public function updateTask()
    {
        $this->reloadTask();
    }
    #[On('select')]
    public function select($id)
    {
        if ($this->tasks->contains($id)) {
            $this->selectedTask = $this->tasks->find($id);
        }
        if ($this->completedTasks->contains($id)) {
            $this->selectedTask = $this->completedTasks->find($id);
        }
    }
    public function reloadTask()
    {
        $this->catalogue->refresh();
        $this->tasks = $this->catalogue->tasks;
        $this->completedTasks = $this->catalogue->tasks()->onlyTrashed()->get();
        $this->completedCount = $this->completedTasks->count();
    }
    public function forceDelete()
    {
        $this->selectedTask->forceDelete();
        $this->reloadTask();
    }
    public function markComplete()
    {
        $this->selectedTask->delete();
        $this->reloadTask();
    }
    public function unmarkComplete()
    {
        $this->selectedTask->restore();
        $this->reloadTask();
    }
    public function markImportant()
    {
        $this->selectedTask->important = !$this->selectedTask->important;
        $this->selectedTask->save();
        $this->selectedTask->refresh();
    }
};
?>
<div x-data="{ showTask: false, deleteConfirm: false }">
    <div :class="{ 'w-[calc(100%-386px)] ': showTask }">
        <div class="px-3 py-2 bg-white  my-2 w-full rounded-lg shadow-lg">
            <form x wire:submit.prevent="submit" class="w-full py-2 shadow-lg">
                <div class="w-full flex items-center">
                    <span class="w-4 h-4 shrink-0 border border-blue-600 inline-block rounded-full"></span>
                    <input type="text" placeholder="Add a task" wire:model="name"
                        class="border-none text-[15px] focus:ring-0 h-8 flex-1 dark:text-black placeholder:text-blue-800 focus:placeholder:text-gray-900">
                    @error('name')
                        <p class="text-red-600">{{ $message }}</p>
                    @enderror
                </div>


            </form>
            <div class="text-black flex items-center justify-between py-2 px-2">
                <div x-data="{ dueDropdown: false, reminderDropdown: false, catalogueMenu: true }" @enlarge.window="catalogueMenu = false"
                    @shrink.window="catalogueMenu = true" class="flex items-center gap-x-1 py-1">
                    <div class="flex items-center gap-x-0.5 py-0.5 px-1 border border-gray-300 cursor-pointer">
                        <div class="h-5 w-5 cursor-pointer relative">
                            <svg @click="dueDropdown = !dueDropdown; reminderDropdown = false"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-full w-full">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                            <div x-show="dueDropdown" x-cloak
                                class="bg-white z-20 w-40 border rounded-sm mt-1.5 md:w-60"
                                :class="{ '-ms-20': catalogueMenu, '-ms-7': !catalogueMenu }">
                                <h5 class="text-center py-2 shadow-lg mb-1 font-semibold text-gray-600">Due</h5>
                                <div class="p-0.5">
                                    <div @click="$wire.setDue('today'); dueDropdown = false;"
                                        class="py-2 text-center border-2 border-transparent hover:border-blue-800">
                                        Today
                                    </div>
                                    <div @click="$wire.setDue('tomorrow'); dueDropdown = false;"
                                        class="py-2 text-center border-2 border-transparent hover:border-blue-800">
                                        Tomorrow
                                    </div>
                                    <div @click="$wire.setDue('next_week'); dueDropdown = false;"
                                        class="py-2 text-center border-2 border-transparent hover:border-blue-800">
                                        Next week
                                    </div>
                                    <div class="relative">
                                        <label for="dateTest"
                                            @click="$refs.datepicker.min = new Date().toISOString().split('T')[0];$refs.datepicker.showPicker(); "
                                            class="cursor-pointer py-2 text-center border-2 border-transparent hover:border-blue-800 block">
                                            Pick a date
                                        </label>
                                        <input type="date" id="dateTest" x-ref="datepicker"
                                            @change="dueDropdown= false;" wire:model.live="date"
                                            class="invisible h-[2px] bg-white w-full relative hover:border hover:border-blue-800 mt-2 p-2 border rounded shadow-md">
                                    </div>
                                    @if (strlen($date) != 0)
                                        <div @click="dueDropdown = false;$wire.set('date', '')"
                                            class="py-2 text-center border-2 border-transparent text-red-600 hover:border-red-500">
                                            Remove due date</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div @click="dueDropdown = !dueDropdown; reminderDropdown = false">{{ $date }}</div>
                    </div>

                    <div class="flex items-center gap-x-0.5 py-0.5 px-1 border border-gray-300 cursor-pointer">
                        <div class="h-5 w-5 cursor-pointer relative">
                            <svg @click="reminderDropdown = !reminderDropdown; dueDropdown = false"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-full w-full">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                            <div x-show="reminderDropdown"
                                class="bg-white z-30 w-40 text-center border rounded-sm  mt-1.5 md:w-60"
                                :class="{ '-ms-20': catalogueMenu, '-ms-14': !catalogueMenu }">
                                <h5 class="text-center py-2 shadow-lg mb-1 font-semibold text-gray-600">Reminder</h5>
                                <div class="p-0.5">
                                    <div @click="reminderDropdown = false;$wire.setReminder('today');"
                                        class="py-2 text-center border-2 border-transparent hover:border-blue-800">Later
                                        Today</div>
                                    <div @click="reminderDropdown = false;$wire.setReminder('tomorrow');"
                                        class="py-2 text-center border-2 border-transparent hover:border-blue-800">
                                        Tomorrow
                                    </div>
                                    <div @click="reminderDropdown = false;$wire.setReminder('next_week');"
                                        class="py-2 text-center border-2 border-transparent hover:border-blue-800">Next
                                        week</div>
                                    <div @click="fp.open(); reminderDropdown = false"
                                        class="py-2 text-center border-2 border-transparent hover:border-blue-800">Pick
                                        a
                                        date & time
                                    </div>
                                    @if (strlen($reminder) != 0)
                                        <div @click="reminderDropdown = false;$wire.set('reminder', '')"
                                            class="py-2 text-center border-2 border-transparent text-red-600 hover:border-red-500">
                                            Remove Reminder</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div @click="reminderDropdown= !reminderDropdown ; dueDropdown = false">
                            {{ $reminder }}
                        </div>
                    </div>
                    <input type="text" id="flatRemind" wire:model.live="reminder"
                        class="placeholder:text-black w-[1px] h-[1px] bg-amber-600 invisible inline-block border-2  placeholder:text-center border-transparent active:ring-0 focus:ring-0">
                    @once
                        <script>
                            let flatRemind = document.querySelector('#flatRemind');

                            const fp = flatpickr(flatRemind, {
                                enableTime: true,
                                dateFormat: "Y-m-d H:i",
                                minDate: "today"
                            });
                        </script>
                    @endonce

                </div>

                <div>
                    <button @click="$wire.submit()" wire:loading.attr="disabled"
                        class="border shadow-lg py-1 disabled:bg-gray-300 px-2 cursor-pointer border-gray-300 rounded-md">
                        Add</button>
                </div>

            </div>
        </div>
        <div>
            @foreach ($tasks as $task)
                <livewire:partials.task :task="$task" :key="$task->id" />
            @endforeach
        </div>
        @if ($completedTasks->count() > 0)
            <div x-data="dropdown">
                <div @click="toggle()" :class="{ 'rounded-b-lg': !open }"
                    class="w-full h-10 bg-gray-200 rounded-t-lg select-none text-black cursor-pointer leading-10 px-3 font-semibold text-sm border-b">
                    <span :class="{ 'rotate-90 mt-1': open, }"
                        class="text-gray-600 inline-flex w-fit items-center justify-center leading-none  font-semibold text-xl ">&gt;</span>
                    Completed
                    <span class="text-gray-800">
                        {{ $completedCount }}
                    </span>
                </div>


                <div x-show="open" class="bg-gray-200 py-3 ">
                    @foreach ($completedTasks as $item)
                        <div @click="showTask = true; $wire.select({{ $item->id }})"
                            class="h-10 cursor-pointer bg-white px-3 py-2 flex items-center my-1.5 shadow-sm rounded-lg">
                            <span
                                class="w-4 h-4 shrink-0 leading-[13px] text-center  text-xs bg-blue-700  text-white inline-block rounded-full ">&check;
                            </span>
                            <span class="line-through text-gray-800 font-semibold ps-3">
                                {{ $item->name }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div x-show="showTask">
        <div>
            <div
                class="fixed top-[66px] shadow-lg right-0  overflow-y-scroll bg-white text-black w-96 max-h-[calc(100vh-112px)] h-[calc(100vh-112px)]">
                <div class="w-full h-full">

                    @if (isset($selectedTask))
                        @if ($selectedTask->deleted_at == null)
                            <div
                                class="h-10 py-2 my-1 px-3 w-full rounded-lg shadow-sm cursor-pointer bg-white text-black  text-sm flex items-center justify-between gap-x-3">
                                <span @click="$wire.markComplete()"
                                    class="w-4 h-4 shrink-0 leading-[13px] text-center text-black/0 text-xs hover:text-blue-700 border cursor-pointer border-blue-600 inline-block rounded-full ">&check;
                                </span>
                                <span class="text-gray-800 font-semibold ps-3"> {{ $selectedTask?->name }} </span>
                                <span class="text-blue-700 ">
                                    @if ($selectedTask->important === 1)
                                        <svg @click="$wire.markImportant()" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                            <path fill-rule="evenodd"
                                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg @click="$wire.markImportant()" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                        </svg>
                                    @endif

                                </span>
                            </div>
                        @else
                            <div
                                class="h-10 py-2 my-1 px-3 w-full rounded-lg shadow-sm cursor-pointer bg-white text-black  text-sm flex items-center justify-between gap-x-3">
                                <span @click="$wire.unmarkComplete()"
                                    class="w-4 h-4 shrink-0 leading-[13px] text-center  text-xs bg-blue-700  text-white inline-block rounded-full ">&check;
                                </span>
                                <span class="line-through text-gray-800 font-semibold ps-3">
                                    {{ $selectedTask?->name }}
                                </span>
                                <span class="text-blue-700 ">
                                    @if ($selectedTask->important === 1)
                                        <svg @click="$wire.markImportant()" xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5">
                                            <path fill-rule="evenodd"
                                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg @click="$wire.markImportant()" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                            stroke="currentColor" class="h-5 w-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                                        </svg>
                                    @endif

                                </span>
                            </div>
                        @endif
                    @endif

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
                    <span @click="showTask = false"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="h-5 w-5 cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                        </svg>
                    </span>
                    <span class="font-semibold text-gray-800">Created Today</span>
                    <span><svg @click="deleteConfirm = true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            class="h-5 w-5 cursor-pointer">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div x-show="deleteConfirm" class="absolute inset-0 bg-gray-200/20 flex items-center justify-center">
        <div class="bg-white w-80 dark:text-black border shadow-lg h-40">
            <h1 class="p-2 my-1"> some tent</h1>
            <p class="p-2 text-gray-700 text-xs">You won't be able to undo this action.</p>
            <div class="flex justify-end mt-4">
                <div class="flex px-2  ">
                    <button @click="deleteConfirm = false"
                        class="p-2 bg-gray-100 hover:border-gray-700 border border-transparent  rounded-lg cursor-pointer  mx-2">Cancel</button>
                    <button @click="deleteConfirm = false;showTask = false; $wire.forceDelete()"
                        class="p-2 bg-red-600 font-semibold text-white  rounded-lg cursor-pointer mx-2">Delete
                        task</button>
                </div>
            </div>
        </div>
    </div>

</div>
