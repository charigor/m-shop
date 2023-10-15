@if($nav)
    @foreach($nav as $key => $value)
        @if($value)
            <button wire:click="clearGroupFilter('{{$key}}')"
                    class="relative flex flex-wrap items-center p-0.5 mr-2 overflow-hidden text-sm font-medium text-gray-900 dark:text-white">
              <span
                  class="relative text-xs px-2 py-1 mb-1 hover:text-gray-700 transition-all ease-in duration-75 bg-white border dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                  {{$translation[$key]}}:
              </span>
                @foreach($value as $name => $item)

                    <span wire:click.stop="clearElementFilter('{{$key}}','{{$name}}')"
                          class="ml-1 mb-1 whitespace-nowrap relative px-1 py-1 text-xs text-white transition-all ease-in duration-75 bg-green-500 hover:bg-green-400 border dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
                           <span>{{$name}} <i class="mdi mdi-close text-xs align-bottom"></i></span>
                    </span>
                @endforeach
            </button>
        @endif
    @endforeach
@endif
@if($nav)
    <button wire:click.prevent="clearAllFilters"
            class="relative inline-flex items-center justify-center p-0.5 mb-2 mr-2 overflow-hidden text-sm font-medium text-gray-900 dark:text-white">
          <span
              class="relative text-xs px-2 py-1 hover:text-gray-700 transition-all ease-in duration-75 bg-white border dark:bg-gray-900 rounded-md group-hover:bg-opacity-0">
               Clear Filter
          </span>
    </button>
@endif
