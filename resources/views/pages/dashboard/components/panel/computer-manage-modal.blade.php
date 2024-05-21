<x-modal :modalId="'computer-manage'">



</x-modal>


<script>
    var allClients = {!! \App\Models\Client::all() !!};
</script>

<script id="computer-manage-modal" type="text/template">
    <x-form :id="'computer-manage-form'">
        <div data-content-wrapper>
            <div class="flex py-3 py-3 border-b border-gray-300">
                <button class="text-xl text-white p-2 rounded-sm cursor-pointer [&.active]:bg-gray-700 hover:bg-gray-700 active" data-content-nav data-id="computer-current">Текущий статус</button>
                <button class="text-xl text-white p-2 rounded-sm cursor-pointer [&.active]:bg-gray-700 hover:bg-gray-700" data-content-nav data-id="computer-booking">Бронь</button>
            </div>

            <div data-content data-id="computer-current" class="active py-3">

                <div class="mb-2">
                    <span class="text-white">Статус:</span>
                    <span class="text-<%= computer.color  %>-500">
                        <span><%= computer.status.name  %></span>
                        <% if(computer.status.alias != 'free') { %>
                            <span>до <%= moment(computer.booking.end_time).format('MM.DD HH:mm') %> ч.</span>
                        <% } %>
                    </span>
                </div>

                <x-form-error :name="'dublicate'"></x-form-error>
                <input type="hidden" name="time" value="0">
                <div class="mb-3">
                    <span class="text-gray-300">Продолжительность: </span>
                    <span data-time="0" class="text-white">0 мин.</span>
                </div>
                <div class="mb-2">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <div data-time-plus="10" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 10 минут</div>
                        <div data-time-plus="30" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 30 минут</div>
                        <div data-time-plus="60" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 1 час</div>
                        <div data-time-plus="120" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 2 часа</div>
                        <div data-time-plus="300" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 5 часов</div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <div data-time-minus="10" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 10 минут</div>
                        <div data-time-minus="30" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 30 минут</div>
                        <div data-time-minus="60" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 1 час</div>
                        <div data-time-minus="120" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 2 часа</div>
                        <div data-time-minus="300" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 5 часов</div>
                    </div>
                </div>
                <% if(computer.status.alias == 'occupied') { %>
                    <div class="flex items-center gap-2 mb-3">
                        <x-form-button :type="'button'" onclick="computerMakeFree(this.closest('form'))">Освободить</x-form-button>
                        <x-form-button :type="'button'" onclick="computerExtend(this.closest('form'))">Продлить</x-form-button>
                    </div>
                <% } else { %>
                    <x-form-item>
                        <x-form-label>Клиент</x-form-label>
                        <div class="flex items-start gap-4">
                            <x-form-button :type="'button'" onclick="openClientForm()">Добавить</x-form-button>
                            <div>
                                <x-form-select name="client_id" id="client_select">
                                    <option>Не выбрано</option>
                                    <% allClients.map(client => { %>
                                        <option value="<%= client.id %>" <%= computer.client && computer.client.id == client.id ? 'selected' : '' %>><%= client.name %></option>
                                    <% }) %>
                                </x-form-select>
                                <x-form-error :name="'client_id'"></x-form-error>
                            </div>
                        </div>
                        <div id="client-form" class="hidden" data-form-id="{{ Str::uuid() }}">
                            <x-form-item>
                                <x-form-label>Имя</x-form-label>
                                <x-form-input :type="'text'" :name="'name'" />
                            </x-form-item>
                            <x-form-item>
                                <x-form-label>Телефон</x-form-label>
                                <x-form-input :type="'tel'" :name="'phone'" />
                            </x-form-item>
                            <x-form-item>
                                <x-form-label>Почта</x-form-label>
                                <x-form-input :type="'email'" :name="'email'" />
                            </x-form-item>

                            <x-form-button :type="'button'" :data="'data-submit-btn'" onclick="addClient(this.closest('#client-form'))">Добавить</x-form-button>
                        </div>
                    </x-form-item>
                    <div class="flex flex-wrap items-start gap-2">
                        <x-form-item>
                            <x-form-label>Дата</x-form-label>
                            <x-form-input :type="'date'" :name="'start_date'" :value="'<%= moment().format(`YYYY-MM-DD`) %>'" />
                        </x-form-item>
                        <x-form-item>
                            <x-form-label>Время</x-form-label>
                            <x-form-input :type="'time'" :name="'start_time'" :value="'<%= moment().format(`HH:mm`) %>'" />
                            <x-form-error :name="'start_time'"></x-form-error>
                        </x-form-item>
                    </div>
                    <x-form-button :data="'data-submit-btn'" :type="'button'" onclick="computerMakeBusy(this.closest('form'))">Занять</x-form-button>
                <% } %>
            </div>
        </x-form>
        <x-form :id="'computer-booking-form'">
            <div data-content data-id="computer-booking" class="">
                <div class="py-3 border-b border-gray-300">
                    <input type="hidden" name="time" value="0">
                    <x-form-error :name="'dublicate'"></x-form-error>
                    <div class="mb-3">
                        <span class="text-gray-300">Продолжительность: </span>
                        <span data-time="0" class="text-white">0 мин.</span>
                    </div>
                    <div class="mb-2">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <div data-time-plus="10" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 10 минут</div>
                            <div data-time-plus="30" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 30 минут</div>
                            <div data-time-plus="60" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 1 час</div>
                            <div data-time-plus="120" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 2 часа</div>
                            <div data-time-plus="300" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">+ 5 часов</div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <div data-time-minus="10" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 10 минут</div>
                            <div data-time-minus="30" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 30 минут</div>
                            <div data-time-minus="60" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 1 час</div>
                            <div data-time-minus="120" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 2 часа</div>
                            <div data-time-minus="300" class="cursor-pointer transition-all p-2 rounded-xl text-white bg-black hover:bg-white hover:text-black">- 5 часов</div>
                        </div>
                    </div>
                    <x-form-item>
                        <x-form-label>Клиент</x-form-label>
                        <div class="flex items-start gap-4">
                            <x-form-button :type="'button'" onclick="openClientForm()">Добавить</x-form-button>
                            <div>
                                <x-form-select name="client_id" id="client_select">
                                    <option>Не выбрано</option>
                                    <% allClients.map(client => { %>
                                        <option value="<%= client.id %>" <%= computer.client && computer.client.id == client.id ? 'selected' : '' %>><%= client.name %></option>
                                    <% }) %>
                                </x-form-select>
                                <x-form-error :name="'client_id'"></x-form-error>
                            </div>
                        </div>
                        <div id="client-form" class="hidden" data-form-id="{{ Str::uuid() }}">
                            <x-form-item>
                                <x-form-label>Имя</x-form-label>
                                <x-form-input :type="'text'" :name="'name'" />
                            </x-form-item>
                            <x-form-item>
                                <x-form-label>Телефон</x-form-label>
                                <x-form-input :type="'tel'" :name="'phone'" />
                            </x-form-item>
                            <x-form-item>
                                <x-form-label>Почта</x-form-label>
                                <x-form-input :type="'email'" :name="'email'" />
                            </x-form-item>

                            <x-form-button :type="'button'" :data="'data-submit-btn'" onclick="addClient(this.closest('#client-form'))">Добавить</x-form-button>
                        </div>
                    </x-form-item>
                    <div class="flex flex-wrap items-start gap-2 mb-4">
                        <x-form-item :mb="'0'">
                            <x-form-label>Дата</x-form-label>
                            <x-form-input :type="'date'" :name="'start_date'" value="<%= computer.freeTimes.length ? moment(computer.freeTimes[0].times[0].start).format('YYYY-MM-DD') : '' %>" />
                        </x-form-item>
                        <x-form-item :mb="'0'">
                            <x-form-label>Время</x-form-label>
                            <x-form-input :type="'time'" :name="'start_time'" value="<%= computer.freeTimes.length ? moment(computer.freeTimes[0].times[0].start).format('HH:mm') : '' %>" />
                            <x-form-error :name="'start_time'"></x-form-error>
                        </x-form-item>
                    </div>
                    <div class="mb-4">
                        <% if(computer.freeTimes.length) { %>
                        <div class="text-lg mb-2">
                            Свободное время
                        </div>
                        <div class="mb-2">
                                <x-form-select data-free-time-select class="p-2 rounded-sm outline-none">
                                <% computer.freeTimes.map((freeDate, i) => { %>
                                    
                                    <option value="<%= i %>" <%= i == 0 ? 'selected' : '' %>><%= moment(freeDate.date).format('DD.MM.YYYY') %></option>
                                    
                                <% }) %>
                                </x-form-select>
                        </div>
                        <% } %>
                        <div class="">
                            <% if(computer.freeTimes.length) { %>
                                <% computer.freeTimes.map((freeDate, i) => { %>
                                        <div data-free-date data-id="<%= i %>" class="flex-wrap items-stretch gap-4 text-white <%= i == 0 ? 'active' : '' %>">
                                            <% freeDate.times.map((freeTime, i) => { %>
                                                <% if(freeTime.end) { %>
                                                    <%
                                                        var start = moment(freeTime.start);
                                                        var end = moment(freeTime.end); // another date
                                                        var duration = moment.duration(end.diff(start));

                                                        var hours = duration.hours();
                                                        duration.subtract(moment.duration(hours,'hours'));

                                                        var minutes = duration.minutes();
                                                        duration.subtract(moment.duration(minutes,'minutes'));
                                                        freeTime.time = (hours != 0 ? hours + ' ч. ' : '') + minutes + ' мин.'
                                                    %>
                                                <% } else { %>
                                                    <% freeTime.time = 'Неограничено' %>
                                                <% } %>
                                                <div data-free-time="<%= moment(freeTime.start).format('HH:mm') %>" data-free-date="<%= moment(freeTime.start).format('YYYY-MM-DD') %>" class="flex flex-col p-2 text-white border rounded-sm border-white cursor-pointer hover:bg-white hover:text-black [&.active]:bg-white [&.active]:text-black">
                                                    <div>
                                                        <span>Общ. время:</span>
                                                        <span><%= freeTime.time %></span>
                                                    </div>
                                                    <div class="text-green-500">
                                                        <span>С</span>
                                                        <span><%= moment(freeTime.start).format('DD.MM HH:mm') %></span>
                                                    </div>
                                                    <% if(freeTime.end) { %>
                                                    <div class="text-red-500">
                                                        <span>По</span>
                                                        <span><%= moment(freeTime.end).format('DD.MM HH:mm') %></span>
                                                    </div>
                                                    <% } %>
                                                </div>
                                            <% }) %>
                                        </div>
                                <% }) %>
                            <% } %>
                        </div>
                    </div>
                    <x-form-button :type="'button'" onclick="computerBooking(this.closest('form'))">Забронировать</x-form-button>
                </div>
                <% if(computer.nearest.length) { %>
                    <div class="py-3">
                        <div class="mb-2 text-xl text white">Будущие брони</div>
                        <div class="flex flex-wrap gap-2">
                            <% computer.nearest.map(nearestItem => { %>
                                <div data-booking data-id="<%= nearestItem.id %>" class="mb-2 border border-white rounded-sm p-2 min-w-52">
                                    <div class="mb-2">
                                        <div class="mb-1 text-md text-white"><%= nearestItem.client.name %></div>
                                        <div class="mb-1 text-gray-300"><%= moment(nearestItem.start_time).format('DD.MM HH:mm') %></div>
                                        <div class="mb-1 text-gray-300"><%= moment(nearestItem.end_time).format('DD.MM HH:mm') %></div>
                                    </div>
                                    <div>
                                        <x-form-button :bg="'red'" :type="'button'" onclick="deleteBooking(<%= nearestItem.id %>)">Удалить</x-form-button>
                                    </div>
                                </div>
                            <% }) %>
                        </div>
                    </div>
                <% } %>
            </div>
            </x-form>
        </div>
    
</script>
