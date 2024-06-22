@php
    $computers = \App\Models\Computer::query()->orderBy('sort')->get();
    $statuses = \App\Models\Status::query()->whereIn('alias', ['free', 'occupied'])->get();
    \App\Http\Services\ComputerService::setStatusesAndClients($computers)::setNearest($computers);
@endphp


<div data-content data-id="computers" class="active flex-1 border border-gray-500 rounded-sm px-2 py-2">
    
    @include('pages.dashboard.components.panel.filter')

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 py-4" id="computer-list">
        @foreach ($computers as $computer)
            <div class="px-2 py-2 shadow-sm border border-gray-500 rounded-sm aspect-square text-white sortable-ghost cursor-pointer flex flex-col justify-between"
                data-sort="{{ $computer->sort }}" data-computer-card data-id="{{ $computer->id }}">
                <div>
                    <div class="outline-none break-all mb-4"
                        oninput="debouncedChangeComputerName({{ $computer->id }}, this.innerHTML)"
                        contenteditable="true">
                        {{ $computer->name }}</div>
                    <div class="mb-2">
                        <span class="text-gray-500">Статус:</span>
                        <span class="text-{{ $computer->color }}-700">{{ $computer->status->name }}</span>

                    </div>
                    @if (!empty($computer->client))
                        <div class="mb-2">
                            <span class="text-gray-500">Клиент:</span>
                            <span class="text-white">{{ $computer->client->name }}</span>
                        </div>
                        <div class="mb-2">
                            <span class="text-gray-500">Освободится:</span>
                            <span
                                class="text-white">{{ \Carbon\Carbon::parse($computer->booking->end_time)->locale('ru')->translatedFormat('d.m H:i') }}</span>
                        </div>
                    @endif
                </div>
                <x-button-secondary onclick="openComputerMenu({{ $computer->id }})">Просмотр</x-button-secondary>
            </div>
        @endforeach

        <div data-add-computer-button
            class="non-sortable px-2 shadow-sm border border-gray-500 rounded-sm aspect-square flex items-center justify-center text-white hover:text-black hover:bg-white cursor-pointer"
            onclick="addComputer()">+</div>
    </div>
</div>

@include('pages.dashboard.components.panel.computer-card-lodash')

@include('pages.dashboard.components.panel.computer-menu-modal')

@include('pages.dashboard.components.panel.computer-manage-modal')

@include('pages.dashboard.components.panel.computer-edit-modal')
