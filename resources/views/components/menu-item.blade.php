   @props(['name', 'direct'])

   <a href="{{ route($direct) }}" @class([
       'py-2 px-3 w-full relative before:absolute
                        before:left-0  before:-mt-2 before:h-0 before:bg-blue-500 before:w-1 ',
       'bg-gray-200 dark:bg-gray-300/10  before:animate-stand before:h-full' => request()->routeIs(
           $direct),
   ])>
       {{ $name }}
   </a>
