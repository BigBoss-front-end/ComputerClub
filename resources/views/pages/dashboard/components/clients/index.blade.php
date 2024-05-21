@php
    $clients = \App\Models\Client::query()->get();
@endphp
<div data-content data-id="clients" class="border border-gray-500 rounded-sm px-2 py-2 flex-1">
    <div id="client-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        <div class="flex items-center justify-center text-white p-2 max-w-86 w-full border border-gray-500 rounded-sm cursor-pointer hover:bg-white hover:text-black"
            data-modal-target="client-add">
            +
        </div>
        @foreach ($clients as $client)
            <div class="p-2 max-w-86 w-full border border-gray-500 rounded-sm text-white">
                <div>
                    <span class="text-gray-500">Имя:</span>
                    <span>{{ $client->name }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Телефон:</span>
                    <span>{{ $client->phone }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Почта:</span>
                    <span>{{ $client->email }}</span>
                </div>
            </div>
        @endforeach
    </div>

</div>
