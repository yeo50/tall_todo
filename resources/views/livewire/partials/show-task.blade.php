<?php

use Livewire\Volt\Component;

use App\Models\Task;
use App\Models\TaskBrief;
use Carbon\Carbon;

new class extends Component {
    public $id;

    public $name;
    public $deleted_at;
    public $important;
    public $reminder;
    public $due;

    public $task;

    public $outline;
    public $note;

    public function mount()
    {
        $this->task = Task::withTrashed()->with('taskBriefs')->find($this->id);
        if ($this->task) {
            $this->fill($this->task->only('name', 'deleted_at', 'important', 'reminder', 'due'));
        } else {
            $this->fill(['name' => 'No Task Selected', 'deleted_at' => null, 'important' => false, 'reminder' => null]);
        }
    }

    public function markComplete()
    {
        if ($this->task) {
            $this->task->delete();
            $this->deleted_at = $this->task->deleted_at;
            $this->dispatch('reload-task');
        } else {
            return;
        }
    }
    public function unmarkComplete()
    {
        if ($this->task) {
            $this->task->restore();
            $this->deleted_at = $this->task->deleted_at;
            $this->dispatch('reload-task');
        } else {
            return;
        }
    }
    public function markImportant()
    {
        $this->task->important = !$this->task->important;
        $this->task->save();
        $this->task->refresh();
        $this->important = $this->task->important;
        $this->dispatch('load-important');
    }

    public function setDue($dateString)
    {
        if ($dateString === 'today') {
            $this->due = Carbon::today()->toDateString();
        }
        if ($dateString === 'tomorrow') {
            $this->due = Carbon::tomorrow()->toDateString();
        }
        if ($dateString === 'next_week') {
            $this->due = Carbon::now()->addWeek()->toDateString();
        }

        $this->task->due = $this->due;
        $this->task->save();
        $this->task->refresh();
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
        $this->task->reminder = $this->reminder;
        $this->task->save();
        $this->task->refresh();
    }
    public function addOutline()
    {
        $taskBrief = new TaskBrief();
        $taskBrief->task_id = $this->id;
        $taskBrief->outline = $this->outline;
        $taskBrief->save();
        return;
    }
    public function addNote()
    {
        $taskBrief = new TaskBrief();
        $taskBrief->task_id = $this->id;
        $taskBrief->note = $this->note;
        $taskBrief->save();
        $this->note = '';
        return;
    }
    public function changeName()
    {
        $this->task->name = $this->name;
        $this->task->save();
        $this->dispatch('reload-task');
        return;
    }
};

?>

<div>

    <div
        class="fixed top-[66px] shadow-lg right-0  overflow-y-auto  bg-white text-black dark:bg-gray-800 dark:text-white w-full sm:w-72 md:w-80 lg:w-96 max-h-[calc(100vh-112px)] h-[calc(100vh-112px)]">
        <div>
            <div wire:loading.remove
                class="h-14 py-2 my-1 px-3 w-full rounded-lg shadow-sm cursor-pointer bg-white text-black dark:bg-gray-800 dark:text-white text-sm flex items-center justify-between gap-x-3">

                @if ($deleted_at === null)
                    <span wire:click="markComplete"
                        class="w-4 h-4 shrink-0 leading-[13px] text-center text-black/0 text-xs hover:text-blue-700 border cursor-pointer border-blue-600 inline-block rounded-full ">&check;
                    </span>
                    <div x-data="{ open: true }">
                        <span x-cloak x-show="open" @click="open =false; setTimeout(()=>$refs.nameInput.focus(),50)"
                            class="  font-semibold ps-3 flex-1 text-center">
                            {{ $name }}
                        </span>
                        <input x-cloak x-show="!open" class="border-0 focus:ring-0 dark:bg-gray-800 dark:text-white"
                            onmousedown="return false;" onselectstart="return false;" wire:model="name"
                            @keydown.enter="$wire.changeName(); open = true " x-ref="nameInput" type="text"
                            value="{{ $name }}">
                    </div>
                @else
                    <span wire:click="unmarkComplete"
                        class="w-4 h-4 shrink-0 leading-[13px] text-center  text-xs bg-blue-700  text-white inline-block rounded-full ">&check;
                    </span>
                    <div x-data="{ open: true }">
                        <span x-cloak x-show="open" @click="open =false; setTimeout(()=>$refs.nameInput.focus(),50)"
                            class="line-through  font-semibold ps-3 flex-1 text-center">
                            {{ $name }}
                        </span>
                        <input x-cloak x-show="!open" class="border-0 focus:ring-0 dark:bg-gray-800 dark:text-white"
                            onmousedown="return false;" onselectstart="return false;" wire:model="name"
                            @keydown.enter="$wire.changeName(); open = true " x-ref="nameInput" type="text"
                            value="{{ $name }}">
                    </div>
                @endif

                <span class="text-blue-700 ">
                    @if ($important === 1)
                        <svg wire:click="markImportant()" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                            fill="currentColor" class="h-5 w-5">
                            <path fill-rule="evenodd"
                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z"
                                clip-rule="evenodd" />
                        </svg>
                    @else
                        <svg wire:click="markImportant()" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    @endif
                </span>
            </div>

            <div class="flex items-center justify-center w-full my-1 ">
                <div wire:loading role="status" class="h-15 py-4 flex items-center justify-center">
                    <svg aria-hidden="true" class="w-5 h-5  text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>

                </div>
            </div>

            <div x-data="{ display: true, action: false, inputValue: '' }" class=" py-3 px-3  cursor-pointer ">
                <div class="flex items-center justify-between  gap-x-2 w-full border rounded-lg shadow-lg p-2 ">
                    <span><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </span>
                    <input type="text" wire:model="outline" x-model="inputValue" x-ref="actionInput"
                        class="border-0 flex-1 focus:ring-0 placeholder:text-blue-600 dark:bg-gray-800  focus:placeholder:text-gray-400"
                        placeholder="Add Outline">
                    <button @click="$wire.addOutline(); inputValue = ''"
                        class="min-w-16 py-1.5 rounded-lg border border-gray-300 cursor-pointer"
                        :disabled="!inputValue.trim()" :class="!inputValue.trim() ? 'invisible' : ''">Add</button>
                </div>
                @isset($task->taskBriefs)
                    @if ($task->taskBriefs->count() > 0)
                        <div class="mt-2" x-data="dropdown">
                            <div @click="toggle()" class="p-2 shadow-lg border rounded-lg">
                                <span :class="{ 'rotate-90 translate-y-0.5 ': open, }"
                                    class="text-gray-600 dark:text-gray-400 inline-flex w-fit items-center justify-center leading-none  font-semibold text-xl  transition-transform duration-500 ease-in-out ">&gt;</span>
                                {{ "$name's outlines" }}
                            </div>
                            <div x-show="open" class="p-2">
                                @foreach ($task->taskBriefs as $key => $item)
                                    <p class="p-1">{{ $key + 1 }} {{ $item->outline }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endisset
            </div>

            <div>



            </div>


            <div x-data="{ dueDropdown: false, reminderDropdown: false }">
                <ul class="space-y-1">
                    <li>
                        <div class="flex items-center gap-x-0.5 py-3 px-3 border  border-gray-300 cursor-pointer">
                            <div class="h-5 w-5 cursor-pointer relative">
                                <svg @click="dueDropdown = !dueDropdown; reminderDropdown = false"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="h-full w-full">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                </svg>
                                <div x-show="dueDropdown" x-cloak
                                    class="bg-white dark:bg-gray-800 dark:text-white z-20 w-40 md:w-48 lg:w-60 border rounded-sm absolute -top-[100px] left-[60px] lg:left-[100px]  ">
                                    <h5
                                        class="text-center py-2 shadow-lg mb-1 font-semibold text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        Due</h5>
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
                                                @change="dueDropdown= false; $wire.setDue('custom')"
                                                wire:model.live="due" class="invisible h-0  absolute bottom-0">
                                        </div>
                                        @if (strlen($reminder) != 0)
                                            <div @click="dueDropdown = false;$wire.set('due', '')"
                                                class="py-2 text-center border-2 border-transparent text-red-600 hover:border-red-500">
                                                Remove due date</div>
                                        @endif

                                    </div>
                                </div>
                            </div>

                            <div @click="dueDropdown = !dueDropdown; reminderDropdown = false" class="ps-3 flex-1">
                                {{ $due ? $due : 'Add due date' }}</div>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center gap-x-0.5 py-3 px-3 border border-gray-300 cursor-pointer">
                            <div class="h-5 w-5 cursor-pointer relative">
                                <svg @click="reminderDropdown = !reminderDropdown; dueDropdown = false"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="h-full  w-full">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                                <div x-show="reminderDropdown"
                                    class="bg-white dark:bg-gray-800 dark:text-white  z-10 w-40 md:w-48 lg:w-60 text-center border rounded-sm  absolute -top-[100px] left-[60px] lg:left-[100px] ">
                                    <h5
                                        class="text-center py-2 shadow-lg mb-1 font-semibold text-gray-600 dark:text-gray-400">
                                        Reminder
                                    </h5>
                                    <div class="p-0.5">
                                        <div @click="reminderDropdown = false;$wire.setReminder('today');"
                                            class="py-2 text-center border-2 border-transparent hover:border-blue-800">
                                            Later
                                            Today</div>
                                        <div @click="reminderDropdown = false;$wire.setReminder('tomorrow');"
                                            class="py-2 text-center border-2 border-transparent hover:border-blue-800">
                                            Tomorrow
                                        </div>
                                        <div @click="reminderDropdown = false;$wire.setReminder('next_week');"
                                            class="py-2 text-center border-2 border-transparent hover:border-blue-800">
                                            Next
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
                            <div @click="reminderDropdown= !reminderDropdown ; dueDropdown = false" class="ps-3">
                                {{ $reminder ? $reminder : 'Remind me' }}
                            </div>
                        </div>


                    </li>
                </ul>

            </div>

            <div class="mt-1" x-data="{ noteValue: '' }">

                <textarea id="" cols="10" rows="2" placeholder="Add note" wire:model="note"
                    x-model="noteValue"
                    class="w-full resize-none border-gray-300 dark:bg-gray-800 dark:text-white focus:ring-0 placeholder:text-sm placeholder:text-blue-600 focus:placeholder:text-gray-400
                     focus:border-gray-400 active:border-gray-400"></textarea>
                <div class="flex w-full justify-end">
                    <button @click="$wire.addNote(); noteValue = ''" :disabled="!noteValue.trim()"
                        class="p-2 rounded-lg shadow-lg border cursor-pointer disabled:hidden">Add</button>
                </div>

                @isset($task->taskBriefs)
                    @if ($task->taskBriefs->count() > 0 && $task->taskBriefs->contains(fn($brief) => !empty($brief->note)))
                        <div x-data="dropdown" class="p-2">
                            <div @click="toggle()" class="p-2 shadow-lg border rounded-lg">
                                <span :class="{ 'rotate-90 translate-y-0.5 ': open, }"
                                    class="text-gray-600 dark:text-gray-400 inline-flex w-fit items-center justify-center leading-none  font-semibold text-xl  transition-transform duration-500 ease-in-out ">&gt;</span>
                                note list
                            </div>
                            <div x-show="open" class="p-2">
                                @foreach ($task->taskBriefs as $item)
                                    <p class="p-1">{{ $item->note }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endisset
            </div>
        </div>

    </div>
    <div
        class="fixed right-0 bottom-0 bg-white dark:bg-gray-800 dark:text-white border-t w-full sm:w-72 md:w-80   lg:w-96 h-14 flex items-center">
        <div class="my-auto flex justify-between items-center px-2 py-2 text-black dark:text-white w-full">
            <span @click="showTask = false"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                </svg>
            </span>
            <span class="font-semibold text-gray-800 dark:bg-gray-800 dark:text-white">Created Today</span>
            <span>
                <svg wire:loading.remove @click="deleteConfirm = true" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                    class="h-5 w-5 cursor-pointer">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                </svg>
                <div wire:loading role="status">
                    <svg aria-hidden="true"
                        class="w-5 h-5  text-gray-200 animate-spin dark:text-gray-600 fill-blue-600"
                        viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor" />
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill" />
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </span>


        </div>
    </div>
    <div x-show="deleteConfirm" class="fixed inset-0 bg-gray-200/20 flex items-center justify-center">
        <div class="bg-white w-80 flex flex-col justify-evenly dark:text-black border shadow-lg h-40">

            <p class="p-2 text-gray-700 text-xs text-center">You won't be able to undo this action.</p>
            <div class="flex justify-end mt-4">
                <div class="flex px-2  ">
                    <button @click="deleteConfirm = false"
                        class="p-2 bg-gray-100 hover:border-gray-700 border border-transparent  rounded-lg cursor-pointer  mx-2">Cancel</button>
                    <button
                        @click="deleteConfirm = false;showTask = false; $wire.$parent.forceDelete({{ $id }});"
                        class="p-2 bg-red-600 font-semibold text-white  rounded-lg cursor-pointer mx-2">Delete
                        task</button>

                </div>
            </div>
        </div>
    </div>
</div>
