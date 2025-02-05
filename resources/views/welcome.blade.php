<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>
        Sylla To Do
    </title>
    <link rel="shortcut icon" href="./storage/photos/rightArrow.png" type="image/x-icon">
    <!-- Fonts -->


    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased font-sans">
    <header class="flex justify-end items-center h-14 ">
        <div>
            <a href="{{ route('register') }}"
                class="text-lg hover:text-blue-800 font-bold text-blue-600 py-4 px-3">Register</a>
            <a href="{{ route('login') }}" class="text-lg hover:text-blue-800 font-bold text-blue-600 py-4 px-3 ">Log
                in</a>
        </div>
    </header>
    <div class="p-4 w-full h-[calc(100vh-56px)] md:flex">
        <div class="md:hidden">
            <div class="w-28 mx-auto ">
                <img src="./storage/photos/rightArrow.png" alt="arrow" class="block w-fll h-full">
            </div>
            <h1
                class="text-3xl md:text-5xl text-center  bg-gradient-to-r from-sky-600 bg-clip-text to-blue-900  text-transparent py-3 md:py-5 font-bold">
                Sylla To
                Do
            </h1>
        </div>
        <div class="md:flex-1">
            <img src="./storage/photos/11107577_4668855.svg" alt="photo"
                class="w-full h-[350px] md:block md:w-full md:h-full">
        </div>
        <div class=" md:flex-1 mx-auto flex items-center mt-10 space-y-4">

            <div class=" mx-auto flex items-center flex-col space-y-4 w-5/6 lg:w-4/6">
                <div class="hidden md:block w-28 mx-auto">
                    <img src="./storage/photos/rightArrow.png" alt="arrow" class="block w-fll h-full">
                </div>
                <h1
                    class="hidden md:inline-block text-5xl bg-gradient-to-r from-sky-600  to-blue-900 bg-clip-text text-transparent py-5 font-bold">
                    Sylla To
                    Do
                </h1>
                <p class="text-gray-800 font-semibold tracking-wide py-2 text-center">Sylla To Do focus on organizing
                    tasks with
                    outlines.
                </p>
                <a href="{{ route('register') }}">
                    <x-primary-button class="w-40  justify-center py-3"> Get started </x-primary-button>
                </a>
            </div>
        </div>
    </div>
</body>

</html>
