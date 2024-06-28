<div class="py-2 pb-4 border-b border-gray-500">
    <div data-dropdown-wrapper>
        <div data-dropdown-button class="group">
            <x-icon>
                <svg class="" width="100%" height="100%" viewBox="-4.8 -4.8 33.60 33.60" fill="none"
                    xmlns="http://www.w3.org/2000/svg" stroke="#ffffff" stroke-width="0.00024000000000000003">
                    <g id="SVGRepo_bgCarrier" transform="translate(0,0), scale(1)">
                        <rect class="group-[.active]:stroke-white group-[.active]:fill-white" x="-4.8" y="-4.8"
                            width="33.60" height="33.60" rx="3.36" fill="transparent" stroke-width="1"
                            stroke="#ffffff"></rect>
                    </g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round" stroke="#CCCCCC"
                        stroke-width="0.144"></g>
                    <g id="SVGRepo_iconCarrier">
                        <path class="group-[.active]:stroke-white group-[.active]:fill-black" fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M15 10.5A3.502 3.502 0 0 0 18.355 8H21a1 1 0 1 0 0-2h-2.645a3.502 3.502 0 0 0-6.71 0H3a1 1 0 0 0 0 2h8.645A3.502 3.502 0 0 0 15 10.5zM3 16a1 1 0 1 0 0 2h2.145a3.502 3.502 0 0 0 6.71 0H21a1 1 0 1 0 0-2h-9.145a3.502 3.502 0 0 0-6.71 0H3z"
                            fill="#ffffff"></path>
                    </g>
                </svg>
            </x-icon>
        </div>
        <div class="hidden mt-4" data-dropdown-item>
            <form action="" class="flex flex-wrap items-end gap-4" id="computers-filter">
                <div class="w-full flex flex-wrap items-end gap-4">
                    <x-form-item>
                        <x-form-label>Статус</x-form-label>
                        <x-form-input :name="'status_id'" :type="'hidden'" />
    
                        <div class="flex gap-2 group">
                            <div class="p-2 cursor-pointer border rounded-sm hover:bg-gray-600 [&.active]:bg-white active"
                                data-filter-status data-id="" data-tippy-content="Все статусы">
                                <div class="border border-black rounded-full bg-white w-5 h-5"></div>
                            </div>
                            @foreach ($statuses as $status)
                                <div class="p-2 cursor-pointer border rounded-sm hover:bg-gray-600 [&.active]:bg-white"
                                    data-filter-status data-id="{{ $status->id }}"
                                    data-tippy-content="{{ $status->name }}">
                                    <div class="border border-black rounded-full bg-{{ $status->color }}-500 w-5 h-5">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-form-item>
                    <x-form-item class="max-w-64 w-full">
                        <x-form-label>Свободное время</x-form-label>
                        <x-form-input class="max-w-64 w-full" :name="'dates'" :id="'filter-free-dates-input'" />
                    </x-form-item>
                </div>
                
                <x-form-item>
                    <x-form-label>Имя</x-form-label>
                    <x-form-input :name="'name'" />
                </x-form-item>
                <x-form-item>
                    <x-form-label>Клиент</x-form-label>
                    <x-form-input :name="'client_name'" />
                </x-form-item>
            </form>
        </div>
    </div>
</div>
