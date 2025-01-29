<button
    {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center cursor-pointer px-4 py-2 bg-blue-700 dark:bg-gray-200 border border-transparent rounded-md font-bold text-base text-white dark:text-gray-800 uppercase tracking-widest hover:bg-blue-600 dark:hover:bg-white focus:bg-blue-600 dark:focus:bg-white active:bg-blue-800 dark:active:bg-gray-300 focus:outline-hidden focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
