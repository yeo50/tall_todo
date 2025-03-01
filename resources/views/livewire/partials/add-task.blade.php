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
    public $catalogueId;
    public $catalogueName;
    public $catalogue;
    public $tasks;
    public $model;
    public $date;
    public $reminder;
    public $completedTasks;
    public $completedCount;
    public $selectedTask;
    public $selectedTaskId;

    public $keyHistory = [];
    public $keyTasks = [];

    public function mount()
    {
        $catalogue = Catalogue::find($this->catalogueId);

        if ($catalogue->name === 'important') {
            $this->catalogueName = 'important';
            $this->catalogue = Catalogue::where('name', 'other')->first();
            $this->tasks = Task::where('important', 1)->get();
            $this->completedTasks = Task::onlyTrashed()->where('important', 1)->get();
            $this->completedCount = $this->completedTasks->count();
        } else {
            $this->catalogue = $catalogue;
            $this->tasks = $this->catalogue->tasks;
            $this->completedTasks = $this->catalogue->tasks()->onlyTrashed()->get();
            $this->completedCount = $this->completedTasks->count();
        }
        $this->keyHistory[] = 1;
        $this->keyTasks = $this->tasks->pluck('id');
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
        if ($dateString == 'picker') {
            $this->reminder = Carbon::createFromFormat('Y-m-d\TH:i', $this->reminder)->format('Y-m-d H:i:s');
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
        if ($this->catalogueName === 'important') {
            $task->important = true;
            $task = $this->catalogue->tasks()->save($task);
            $this->resetDefault();
            $this->tasks->push($task);
            $this->keyTasks[] = $task->id;
        } else {
            $task = $this->catalogue->tasks()->save($task);
            $this->resetDefault();
            $this->tasks->push($task);
            $this->keyTasks[] = $task->id;
        }
    }
    public function resetDefault()
    {
        $this->name = '';
        $this->date = '';
        $this->reminder = '';
    }
    #[On('soft-deleted')]
    public function updateTask($id)
    {
        $this->reloadTask();
        if ($this->selectedTask !== null && $this->selectedTask->id === $id) {
            $this->selectedTask->refresh();
            $this->showTaskDetail();
        }
    }
    public function restoreDelete($id)
    {
        $this->completedTasks->find($id)->restore();
        $this->reloadTask();
        if ($this->selectedTask !== null && $this->selectedTask->id === $id) {
            $this->selectedTask->refresh();
            $this->showTaskDetail();
        }
    }
    public function showTaskDetail()
    {
        $this->selectedTaskId = $this->selectedTask->id ?? '';
        $this->keyHistory[] = $this->selectedTaskId;
    }

    public function ReRender($id)
    {
        if ($this->keyTasks->contains($this->selectedTaskId)) {
            $index = $this->keyTasks->search($this->selectedTaskId);
            $this->keyTasks[$index] = $id . 're';
            $this->selectedTaskId = null;
        }
    }

    #[On('select')]
    public function select($id)
    {
        $this->keyTasks = $this->tasks->pluck('id');
        if ($this->tasks->contains($id)) {
            $this->ReRender($id);
            $this->selectedTaskId = null;
            $this->selectedTask = $this->tasks->find($id);
            $this->keyTasks = $this->keyTasks->map(fn($task) => $task === $id ? $id . 're' : $task);
            $this->selectedTaskId = $this->selectedTask->id ?? '';
            $this->keyHistory[] = $this->selectedTaskId;
        }
        if ($this->completedTasks->contains($id)) {
            $this->ReRender($id);
            $this->selectedTask = $this->completedTasks->find($id);
            $this->selectedTaskId = $this->selectedTask->id ?? '';
            $this->keyHistory[] = $this->selectedTaskId;
        }
    }

    #[On('reload-task')]
    public function reloadTask()
    {
        if ($this->catalogueName === 'important') {
            $this->tasks = Task::where('important', 1)->get();
            $this->keyTasks = $this->tasks->pluck('id');
            $this->completedTasks = Task::onlyTrashed()->where('important', 1)->get();
            $this->completedCount = $this->completedTasks->count();
        } else {
            $this->tasks = $this->catalogue->tasks;
            $this->keyTasks = $this->tasks->pluck('id');
            $this->completedTasks = $this->catalogue->tasks()->onlyTrashed()->get();
            $this->completedCount = $this->completedTasks->count();
        }
    }

    public function forceDelete($id)
    {
        $this->selectedTask = null;
        $toDel = Task::withTrashed()->findOrFail($id);

        $this->authorize('delete_task', $toDel);
        $del = $toDel->forceDelete();

        if ($del) {
            $this->reloadTask();
        }
        return;
    }

    #[On('load-important')]
    public function loadImportant()
    {
        if ($this->catalogueName === 'important') {
            $this->tasks = Task::where('important', 1)->get();
            $this->completedTasks = Task::onlyTrashed()->where('important', 1)->get();
            $this->completedCount = $this->completedTasks->count();
        }
        return;
    }
};
?>
<div x-data="{ showTask: false, deleteConfirm: false }">


    <div class=" h-[calc(100vh-170px)] overflow-y-auto"
        :class="{ 'w-[calc(100%-288px)] md:w-[calc(100%-320px)] lg:w-[calc(100%-386px)]': showTask }">
        <div class="px-3 py-2 bg-white dark:bg-gray-800 dark:text-white  my-2 w-full rounded-lg shadow-lg">

            <form x wire:submit.prevent="submit" class="w-full py-2 shadow-lg">
                <div class="w-full flex items-center ">
                    <span class="w-4 h-4 shrink-0 border border-blue-600 inline-block rounded-full"></span>
                    <input type="text" placeholder="Add a task" wire:model="name"
                        :class="showTask ? 'sm:flex-1 w-32' : ''"
                        class="border-none text-[15px] focus:ring-0 h-8 flex-1 dark:bg-gray-800 dark:text-white dark:placeholder:text-white placeholder:text-black dark:focus:placeholder:text-gray-100">
                    @error('name')
                        <p class="text-red-600">{{ $message }}</p>
                    @enderror
                </div>


            </form>
            <div class="text-black flex items-center justify-between py-2 px-2">
                <div x-data="{ dueDropdown: false, reminderDropdown: false, catalogueMenu: true }" @enlarge.window="catalogueMenu = false"
                    @shrink.window="catalogueMenu = true" class="flex items-center gap-x-1 py-1">
                    <div
                        class="flex items-center gap-x-0.5 py-0.5 px-1 border border-gray-300 rounded-lg dark:text-white cursor-pointer">
                        <div class="h-5 w-5 cursor-pointer relative">
                            <svg @click="dueDropdown = !dueDropdown; reminderDropdown = false"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-full w-full">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                            </svg>
                            <div x-show="dueDropdown" x-cloak
                                class="bg-white dark:bg-gray-900 dark:text-white z-20 w-40 border rounded-sm mt-1.5 md:w-60"
                                :class="{ '-ms-5': catalogueMenu, '-ms-12': !catalogueMenu }">
                                <h5
                                    class="text-center py-2 shadow-lg mb-1 font-semibold text-gray-600 dark:text-gray-400">
                                    Due</h5>
                                <div class="p-0.5 ">
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
                                            class="invisible h-0 absolute bottom-0">
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

                    <div
                        class="flex items-center gap-x-0.5 py-0.5 px-1 border border-gray-300 rounded-lg dark:text-white cursor-pointer">
                        <div class="h-5 w-5 cursor-pointer relative">
                            <svg @click="reminderDropdown = !reminderDropdown; dueDropdown = false"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="h-full w-full">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                            <div x-show="reminderDropdown"
                                class="bg-white dark:bg-gray-900 dark:text-white z-30 w-40 text-center border rounded-sm  mt-1.5 md:w-60"
                                :class="{ '-ms-5': catalogueMenu, '-ms-14': !catalogueMenu }">
                                <h5
                                    class="text-center py-2 shadow-lg mb-1 font-semibold text-gray-600 dark:text-gray-400">
                                    Reminder</h5>
                                <div class="p-0.5 bg-white dark:bg-gray-900">
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
                                    <div>
                                        <label for="reminderDate"
                                            class="py-2 w-full block cursor-pointer text-center border-2 border-transparent hover:border-blue-800"
                                            @click="$refs.reminderPicker.min = new Date().toISOString().slice(0, 16); $refs.reminderPicker.showPicker()">Pick
                                            a
                                            date & time</label>
                                        <input type="datetime-local" id="reminderDate" wire:model="reminder"
                                            @change="reminderDropdown = false;$wire.setReminder('picker')"
                                            x-ref="reminderPicker" class="invisible h-0 absolute bottom-0">
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

                </div>

                <div>
                    <button @click="$wire.submit()" wire:loading.attr="disabled"
                        class="border dark:text-white font-semibold shadow-lg py-1 disabled:bg-gray-300 px-2 cursor-pointer border-gray-300 rounded-md">
                        Add</button>
                </div>

            </div>
        </div>
        <div>
            @foreach ($tasks as $key => $task)
                <livewire:partials.task :task="$task" :key="$keyTasks[$key] . $task->name" :selectedTaskId="$selectedTaskId" />
            @endforeach
        </div>
        @if ($completedTasks->count() > 0)
            <div x-data="dropdown">
                <div @click="toggle()" :class="{ 'rounded-b-lg': !open }"
                    class="w-full h-10 bg-gray-200 dark:bg-gray-700 dark:text-white rounded-t-lg select-none text-black cursor-pointer leading-10 px-3 font-semibold text-sm border-b dark:border-gray-700">
                    <span :class="{ 'rotate-90 translate-y-0.5': open, }"
                        class="text-gray-600 dark:text-gray-400 inline-flex w-fit items-center justify-center leading-none  font-semibold text-xl
                        transition-transform duration-500 ease-in-out">&gt;</span>
                    Completed
                    <span>
                        {{ $completedCount }}
                    </span>
                </div>


                <div x-show="open" class="py-0.5 bg-gray-200 dark:bg-gray-800 space-y-1 rounded-b-lg">

                    @foreach ($completedTasks as $item)
                        <div @class([
                            'h-10 cursor-pointer hover:bg-gray-300 dark:hover:bg-gray-700   px-3 py-2 flex items-center my-1.5 shadow-sm rounded-lg',
                            'bg-gray-300 dark:bg-gray-700 dark:text-white' =>
                                $item->id == $selectedTask?->id,
                            'bg-white dark:bg-gray-900' => $item->id != $selectedTask?->id,
                        ])>

                            <span @click="$wire.restoreDelete({{ $item->id }})"
                                class="w-4 h-4 shrink-0 leading-[13px] text-center  text-xs bg-blue-700  text-white inline-block rounded-full ">&check;
                            </span>
                            <span
                                @click="$wire.select({{ $item->id }});showTask = true; $dispatch('remove-profile') "
                                class="line-through flex-1  font-semibold ps-3">
                                {{ $item->name }}
                            </span>
                            <span class="text-black" x-text="bgId">

                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    <div x-show="showTask" class="relative" @remove-menu.window="showTask = false">

        <livewire:partials.show-task :id="$selectedTaskId" :key="implode('-', $keyHistory)" />

    </div>


</div>
