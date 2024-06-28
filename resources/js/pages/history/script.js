import { API_HISTORY_DELELE, API_HISTORY_LIST_URL, API_HISTORY_UPDATE, datepickerDefaultOptions, datepickerPeriodOptions } from "../../utils/constants"
import { handleSelectDefaultDatrepicker, handleSelectPeriodDatrepicker, initDatepicker, notify, openHistory, sendingButton } from "../../utils/functions"

$(function() {
    $(document).on('click', '[data-history-row]', function() {
        openHistory($(this).attr('id'))
    })

    $(document).on('change', '#history-filter', function() {
        filter()
    })

    $(document).on('submit', '#save-history-form', function() {
        saveHistory(this)
    })

    initDatepicker('#filter-date-input', {
        ...datepickerPeriodOptions,
        buttons: [
            {
                content: 'За все время',
                className: 'custom-button-classname',
                onClick: (dp) => {
                    let date = new Date();
                    $('#filter-date-input').val('За все время')
                    $('#filter-date-input').siblings('[datepicker-input]').val('').trigger('change')
                }
            }
        ],
        onSelect: (data) => {
            handleSelectPeriodDatrepicker(data)
        }
    })

    filter()
})

window.openDeleteHistoryModal = (id) => {
    $('#delete-history-button').off('click').on('click', function() {
        deleteHistory(id)
    })
    $('[data-remodal-id="delete-history"]').remodal().open()
}


let isPending = false;
const filter = () => {

    if(isPending) {
        return false
    }

    isPending = true

    let form = document.getElementById('history-filter')

    let data = Object.fromEntries(new FormData(form))

    data.date_begin = data.date_from.split(' - ')[0]
    data.date_from = data.date_from.split(' - ')[1]
    data.group_date = 1
    axios.post(
        API_HISTORY_LIST_URL,
        data
    ).then(r => {     
        $('#history-groups').html('')

        if(!Object.keys(r.data.groups).length) {
            $('#history-groups').append(
                (_.template($('#empty-template').html()))()
            )
        }

        for (const date in r.data.groups) {
            $('#history-groups').append(
                (_.template($('#history-group-template').html()))({
                    date,
                    items: r.data.groups[date],
                })
            )
        }
    }).finally(() => {
        setTimeout(() => {
            isPending = false
        }, 500);
    })
}

var sending = false;
window.saveHistory = (form) => {
    let data = new FormData(form)

    let objectData = Object.fromEntries(data)

    if (objectData.type == 'decrement') {
        data.set('count', objectData.count * -1)
    }

    if(sending) {
        return false
    }
    sending = true;
    sendingButton(form.querySelector('.sending_button'))

    axios.postForm(
        API_HISTORY_UPDATE(objectData.id),
        data
    ).then(r => {
        $(`[data-history-row][id="${r.data.history.id}"]`).replaceWith(
            (_.template($('#history-item-template').html()))(r.data.history)
        )
        $('[data-remodal-id="history-info"]').remodal().close()

        notify(
            "Движение обновлено",
            2000,
        )
    }).catch(r => {
        if(r.response.status == 422) {
            for (const k in r.response.data.errors) {
                const e = r.response.data.errors[k];
                
                let html = '';
                e.map(m => {
                    html += m + '<br>'
                })
                
                if($(form).find(`input[name="${k}"]`).siblings('.input_error').length) {
                    $(form).find(`input[name="${k}"]`).siblings('.input_error').html(html)
                }
            }
        } else {
            $(form).find(`.errors`).removeClass('hidden').html(r.response.data.error)
        }
    }).finally(() => {
        sendingButton(form.querySelector('.sending_button'), true)
        sending = false
    })
}
window.deleteHistory = (id) => {
    axios.delete(
        API_HISTORY_DELELE(id)
    ).then(r => {
        $(`[data-history-row][id="${id}"]`).remove()
        $('[data-remodal-id="history-info"]').remodal().close()
        $('[data-remodal-id="delete-history"]').remodal().close()

        notify(
            "Движение удалено",
            2000,
        )
    })
}