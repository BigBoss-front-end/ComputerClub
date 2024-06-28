<div class="bg-white py-4 ">
    <x-container>
        <div class="flex items-center justify-between">
            <a href="/">
                <x-icon-logo />
            </a>
            {{-- <div class="flex items-center justify-between">
                <button id="burger-menu" class="block focus:outline-none">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
        
            </div> --}}

            <div id="burger-menu">
                <x-burger class="border-2 p-2 rounded-md border-orange-500" :color="'orange-500'" :hover="'bg-orange-600'" :active="'bg-orange-700'" />
            </div>
            
        </div>

    </x-container>
</div>