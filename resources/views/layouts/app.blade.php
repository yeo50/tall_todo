<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Sylla To Do</title>

    <link rel="shortcut icon" href="./storage/photos/rightArrow.png" type="image/x-icon">


    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white dark:bg-gray-800 shadow-sm">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main class="flex ">
            <section x-data="{ open: true }" x-show="open" class=" dark:text-white sm:w-60 lg:w-72 ">
                <div @click="open = false" class="dark:text-white cursor-pointer py-2 ps-6  w-14 h-14">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-full h-full">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </div>
                <div class="flex flex-col ">
                    <x-menu-item name="Work" direct="work" />
                    <x-menu-item name="Important" direct="important" />
                    <x-menu-item name="Routine" direct="routine" />

                </div>
                <template x-teleport='.destination' @click="open = true">
                    <div x-show="!open" class="dark:text-white cursor-pointer py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </div>
                </template>
            </section>
            <section class="dark:text-white p-4 flex-1  bg-gray-100 dark:bg-gray-900">
                <div class="flex items-center mb-6">
                    <div class="destination"></div>
                    @if (isset($pageTitleBar))
                        <div class="ps-4 text-2xl font-bold dark:text-white">
                            {{ $pageTitleBar }}
                        </div>
                    @endif
                </div>
                {{ $slot }}
            </section>
        </main>
    </div>
</body>

</html>
