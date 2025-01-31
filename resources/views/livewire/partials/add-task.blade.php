<?php

use App\Models\Task;
use App\Models\Catalogue;
use Livewire\Volt\Component;

new class extends Component {
    public $name;
    public $catalogueName;
    public $catalogue;
    public $tasks;
    public $model;
    public $date;
    public function mount()
    {
        $this->catalogue = Catalogue::where('name', $this->catalogueName)->first();
        $this->tasks = $this->catalogue->tasks;
    }
    public function submit()
    {
        $task = new Task(['catalogue_id' => $this->catalogue->id, 'name' => $this->name]);
        $this->catalogue->tasks()->save($task);
        $this->catalogue->refresh();
        $this->name = '';
        $this->tasks = $this->catalogue->tasks;
    }
};
?>
<div>
    <div class="px-3 py-2 bg-white my-2 w-full rounded-lg shadow-lg">
        <form x-data="{ refreshKey: 0 }" wire:submit.prevent="submit" class="w-full py-2 shadow-lg"
            @submit-form.window="$wire.submit(); refreshKey++">
            <div class="w-full flex items-center">
                <span class="w-4 h-4 shrink-0 border border-blue-600 inline-block rounded-full"></span>
                <input type="text" placeholder="Add a task" wire:model="name"
                    class="border-none text-[15px] focus:ring-0 h-8 flex-1 dark:text-black placeholder:text-blue-800 focus:placeholder:text-gray-900">
            </div>


        </form>
        <div class="text-black flex items-center justify-between py-2 px-2">
            <div class="flex items-center gap-x-1 py-1">
                <div class="flex items-center gap-x-0.5 py-0.5 px-1 border border-gray-300">
                    <div x-data="dropdown" @click.outside="open = false" class="h-5 w-5  cursor-pointer relative">
                        <svg @click="toggle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="h-full w-full ">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        <div x-show="open" class="bg-white z-20 w-40 -ms-20 mt-1.5 md:w-60">
                            <div>
                                <h5 class="text-center py-2 shadow-lg mb-1 font-semibold text-gray-600">Due</h5>
                                <div class=" p-0.5">
                                    <div class="py-2 text-center border-2 border-transparent hover:border-blue-800 ">
                                        Today
                                    </div>
                                    <div class="py-2 text-center border-2 border-transparent hover:border-blue-800 ">
                                        Tomorrow
                                    </div>
                                    <div class="py-2 text-center border-2 border-transparent hover:border-blue-800 ">
                                        Next
                                        week
                                    </div>
                                    <div class="relative">
                                        <label for="dateTest"
                                            @click="$refs.datepicker.min = new Date().toISOString().split('T')[0];$refs.datepicker.showPicker(); "
                                            class="cursor-pointer py-2 text-center border-2 border-transparent hover:border-blue-800 block">
                                            Pick a date
                                        </label>
                                        <input type="date" id="dateTest" x-ref="datepicker" @change="open= false;"
                                            wire:model.live="date" placeholder="Pick a date"
                                            class="invisible h-[2px] bg-white w-full relative hover:border hover:border-blue-800 mt-2 p-2 border rounded shadow-md">
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div>{{ $date }}</div>
                </div>
                <div x-data="dropdown" @click.outside="open = false" class="h-5 w-5 cursor-pointer relative">
                    <svg @click="toggle" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="h-full w-full ">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    <div x-show="open" class="bg-red-500 z-30 w-40 -ms-20 mt-1.5 md:w-60">
                        <h5 class="text-center py-2 shadow-lg mb-1 font-semibold text-gray-600">Due</h5>
                        <div class=" p-0.5">
                            <div class="py-2 text-center border-2 border-transparent hover:border-blue-800 ">Today</div>
                            <div class="py-2 text-center border-2 border-transparent hover:border-blue-800 ">Tomorrow
                            </div>
                            <div class="py-2 text-center border-2 border-transparent hover:border-blue-800 ">Next week
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <button @click="$wire.submit()"
                    class="border shadow-lg py-1 px-2 cursor-pointer border-gray-300 rounded-md">
                    Add</button>
            </div>

        </div>
    </div>
    <div>
        @foreach ($tasks as $item)
            <div class="py-2 px-3 w-full rounded-lg shadow-sm bg-white text-black my-1 flex items-center gap-x-3">
                <span
                    class="w-4 h-4 shrink-0 leading-[13px] text-center text-black/0 text-xs hover:text-blue-700 border cursor-pointer border-blue-600 inline-block rounded-full ">&check;
                </span>
                <span>{{ $item->name }}</span>
            </div>
        @endforeach
    </div>
</div>
