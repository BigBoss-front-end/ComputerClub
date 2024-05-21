import axios from "axios"
import {
    CREATE_BOOKING_URL,
    CREATE_CLIENT_URL,
    CREATE_COMPUTER_URL,
    DELETE_BOOKING_URL,
    DELETE_COMPUTER_URL,
    GET_COMPUTER_LIST,
    GET_COMPUTER_URL,
    UPDATE_BATCH_COMPUTER_URL,
    UPDATE_BOOKING_URL,
    UPDATE_COMPUTER_URL
} from "../../../utils/constants"
import { closeModal, openModal } from "../../../utils/modal"
import { formSubmit } from "../../../utils/functions"
import { createSortable } from "../../../utils/sortablejs"
import { filter } from "lodash"

var computer = {}

var computers = [];

$(function () {
    fetchAllComputers(GET_COMPUTER_LIST).then(r => {
        r.map(computer => {
            renderComputerCard(computer)
        })
    })

    setInterval(() => {
        fetchAllComputers(GET_COMPUTER_LIST).then(r => {
            r.map(computer => {
                renderComputerCard(computer)
            })
        })
    }, 5000);

    createSortable(
        document.getElementById('computer-list'),
        UPDATE_BATCH_COMPUTER_URL
    )

    $(document).on('click', '[data-time-plus]', function () {
        let count = $(this).attr('data-time-plus')

        let currentCount = $('[data-time]').attr('data-time')
        let sum = Number(count) + Number(currentCount)
        let m = sum % 60
        let h = (sum - m) / 60
        let string = h <= 0 && m <= 0 ? '0 мин.' : `${h <= 0 ? '' : h + ' ч. '}${m <= 0 ? '' : (+ ' ' + m + ' мин. ')}`
        $('[data-time]').attr('data-time', sum).html(
            string
        )
        $(this).closest('form').find('input[name="time"]').val(sum)
    })

    $(document).on('click', '[data-time-minus]', function () {
        let count = $(this).attr('data-time-minus')

        let currentCount = $('[data-time]').attr('data-time')
        let sum = Number(currentCount) - Number(count)
        let m = sum % 60
        let h = (sum - m) / 60
        let string = h <= 0 && m <= 0 ? '0 мин.' : `${h <= 0 ? '' : h + ' ч. '}${m <= 0 ? '' : (+ ' ' + m + ' мин. ')}`
        $('[data-time]').attr('data-time', sum >= 0 ? sum : 0).html(
            string
        )
        $(this).closest('form').find('input[name="time"]').val(sum)
    })

    $(document).on('change', '[data-free-time-select]', function () {

        let $current = $(`[data-free-date][data-id="${this.value}"]`)
        $(`[data-free-date][data-id="${this.value}"]`).addClass('active')
        $(`[data-free-date]`).not($current).removeClass('active')
        // let $form = $(this).closest('form')
        // $form.find('input[name="start_date"]').val(moment(this.value).format('YYYY-MM-DD'))
        // $form.find('input[name="start_time"]').val(moment(this.value).format('YYYY-MM-DD'))
    })

    $(document).on('click', '[data-free-time]', function () {
        let $form = $(this).closest('form')
        $form.find('input[name="start_date"]').val(this.getAttribute('data-free-date'))
        $form.find('input[name="start_time"]').val(this.getAttribute('data-free-time'))
    })
})

const getComputers = async (url, page) => {
    const response = await axios.post(url, {filter: {page}});
    console.log(response.data.computers)
    return response.data.computers;
};

const fetchAllComputers = async (url) => {
    let results = [];
    let page = 1;
    let totalPages = 1;

    // Начальный запрос для получения первой страницы и общего числа страниц
    try {
        const initialResponse = await getComputers(url, page);
        results = results.concat(initialResponse.data);
        totalPages = initialResponse.last_page;

        // Запрашиваем остальные страницы
        while (page < totalPages) {
            page += 1;
            const response = await getComputers(url, page);
            results = results.concat(response.data);
            console.log(page)
        }
    } catch (error) {
        console.error(`Ошибка при получении данных: ${error}`);
    }

    return results;
};


const changeComputerName = async (id, name) => {
    await axios.post(
        UPDATE_COMPUTER_URL,
        {
            id,
            name
        }
    ).then(r => {
        getComputer(id).then(res => {
            computer = res.data.computer
        })
        return r
    })
}

window.debouncedChangeComputerName = _.debounce(changeComputerName, 400)

window.addComputer = () => {
    axios.post(
        CREATE_COMPUTER_URL,
    ).then(r => {
        renderComputerCard(r.data.computer)
        return r
    })
}

window.openComputerMenu = (id) => {

    let $modal = $('[data-modal][data-modal-id="computer-menu"]')
    closeModal($(`[data-modal]`).not($modal))
    openModal($modal)
    $modal.find('[data-computer-menu-button]').attr('data-id', id)
}

window.openComputerEditModal = (id) => {
    getComputer(id).then(r => {
        computer = r.data.computer
        closeModal($('[data-modal][data-modal-id="computer-menu"]'))
        let template = (_.template(document.getElementById('computer-edit-modal').innerHTML))({ computer })

        let $modal = $('[data-modal][data-modal-id="computer-edit"]')
        $modal.find('[data-modal-content]').html(template)
        openModal($modal)
    })
}

window.openComputerManageModal = (id) => {
    getComputer(id).then(r => {
        computer = r.data.computer
        closeModal($('[data-modal][data-modal-id="computer-menu"]'))
        let template = (_.template(document.getElementById('computer-manage-modal').innerHTML))({ computer })

        let $modal = $('[data-modal][data-modal-id="computer-manage"]')
        $modal.find('[data-modal-content]').html(template)
        openModal($modal)
    })
}

window.computerMakeFree = (form) => {
    axios.post(
        DELETE_BOOKING_URL,
        { id: computer.booking.id }
    ).then(r => {

        openComputerManageModal(computer.id)
    })
}

window.computerExtend = (form) => {
    let data = Object.fromEntries(new FormData(form))

    let end_date = moment(computer.booking.end_time).add(data.time, 'minutes').format('YYYY-MM-DD HH:mm:ss')

    axios.post(
        UPDATE_BOOKING_URL,
        {
            id: computer.booking.id,
            end_time: end_date,
        }
    ).then(r => {
        getComputer(computer.id).then(r => {
            computer = r.data.computer

            let template = (_.template(document.getElementById('computer-manage-modal').innerHTML))({ computer })
            let $modal = $('[data-modal][data-modal-id="computer-manage"]')
            $modal.find('[data-modal-content]').html(template)
            openModal($modal)
        })
    })
}



window.computerMakeBusy = (form) => {

    let data = Object.fromEntries(new FormData(form))
    console.log(data)

    let start_date = moment(data.start_date + ' ' + data.start_time).format('YYYY-MM-DD HH:mm:ss');
    let end_date = moment(data.start_date + ' ' + data.start_time).add(data.time, 'minutes').format('YYYY-MM-DD HH:mm:ss')

    formSubmit(form, async () => {
        return await axios.post(
            CREATE_BOOKING_URL,
            {
                status_id: 2,
                computer_id: computer.id,
                client_id: data.client_id,
                start_time: start_date,
                end_time: end_date,
            }
        ).then(r => {
            getComputer(computer.id).then(r => {
                computer = r.data.computer

                let template = (_.template(document.getElementById('computer-manage-modal').innerHTML))({ computer })
                let $modal = $('[data-modal][data-modal-id="computer-manage"]')
                $modal.find('[data-modal-content]').html(template)
                openModal($modal)
            })
        })
    })

}

window.computerBooking = (form) => {
    let data = Object.fromEntries(new FormData(form))

    let start_date = moment(data.start_date + ' ' + data.start_time).format('YYYY-MM-DD HH:mm:ss');
    let end_date = moment(data.start_date + ' ' + data.start_time).add(data.time, 'minutes').format('YYYY-MM-DD HH:mm:ss')

    formSubmit(form, async () => {
        return await axios.post(
            CREATE_BOOKING_URL,
            {
                status_id: 3,
                computer_id: computer.id,
                client_id: data.client_id,
                start_time: start_date,
                end_time: end_date,
            }
        ).then(r => {
            getComputer(computer.id).then(r => {
                computer = r.data.computer

                let template = (_.template(document.getElementById('computer-manage-modal').innerHTML))({ computer })
                let $modal = $('[data-modal][data-modal-id="computer-manage"]')
                $modal.find('[data-modal-content]').html(template)
                openModal($modal)
            })

            return r;
        })
    })
}

const getComputer = async (id) => {
    return await axios.post(
        GET_COMPUTER_URL,
        { id }
    ).then(r => {
        renderComputerCard(r.data.computer)
        return r
    })
}

const renderComputerCard = (computer) => {

    let template = (_.template(document.getElementById('computer-card-template').innerHTML))({ computer })
    if ($(`[data-computer-card][data-id="${computer.id}"]`).length) {
        $(`[data-computer-card][data-id="${computer.id}"]`).replaceWith(template)
    } else {
        $('#computer-list [data-add-computer-button]').before(template)
    }
}

window.openClientForm = () => {
    $('#client-form').removeClass('hidden')
}

window.addClient = (formDiv) => {
    let data = {
        name: $(formDiv).find('input[name="name"]').val(),
        phone: $(formDiv).find('input[name="phone"]').val(),
        email: $(formDiv).find('input[name="email"]').val(),
    };

    formSubmit(formDiv, async () => {
        return await axios.post(
            CREATE_CLIENT_URL,
            data,
        ).then(r => {
            let client = r.data.client;
            let $form = $(formDiv).closest('form')
            $form.find('select[name="client_id"]').append(`<option value="${client.id}">${client.name}</option>`).val(client.id)

            $(formDiv).find('input').val('')
            $(formDiv).addClass('hidden')

            return r;
        })
    })
}

window.deteteComputer = (id, form) => {
    formSubmit(form, async () => {
        return await axios.post(
            DELETE_COMPUTER_URL,
            { id },
        ).then(r => {
            $(`[data-computer-card][data-id="${id}"]`).remove()
            closeModal($('[data-modal-id="computer-edit"]'))

            return r;
        })
    })
}