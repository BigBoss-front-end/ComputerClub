<script id="computer-card-template" type="text/template">
    <div class="px-2 py-2 shadow-sm border border-gray-500 rounded-sm aspect-square text-white sortable-ghost cursor-pointer flex flex-col justify-between"
        onclick="openComputerMenu(<%= computer.id %>)" data-computer-card data-id="<%= computer.id %>">
        <div>
            <div class="outline-none break-all mb-4"
                oninput="debouncedChangeComputerName(<%= computer.id %>, this.innerHTML)" 
                contenteditable="true">
                <%= computer.name %></div>
            <div class="mb-2">
                <span class="text-gray-500">Статус:</span>
                <span class="text-<%= computer.color %>-700"><%= computer.status.name %></span>
            </div>
            <% if (computer.client) { %>
                <div class="mb-2">
                    <span class="text-gray-500">Клиент:</span>
                    <span class="text-white"><%= computer.client.name %></span>
                </div>
                <div class="mb-2">
                    <span class="text-gray-500">Освободится:</span>
                    <span
                        class="text-white"><%= moment(computer.booking.end_time).format('DD.MM HH:mm') %></span>
                </div>
            <% } %>
        </div>
        <x-button-secondary onclick="openComputerMenu(<%= computer.id %>)">Просмотр</x-button-secondary>
    </div>
</script>