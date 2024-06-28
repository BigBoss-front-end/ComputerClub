import axios from "axios"
import { API_HISTORY_ADD, API_HISTORY_BY_ID, API_HISTORY_DELELE, API_HISTORY_EXCEL_URL, API_HISTORY_LIST_URL, API_HISTORY_UPDATE, datepickerDefaultOptions } from "../../utils/constants"
import { debounce, handleSelectDefaultDatrepicker, initDatepicker, notify, openHistory, sendingButton } from "../../utils/functions"


$(function () {
    $(document).on('submit', '.remodal-history-add form', function () {
        addHistory(this)
    })

    $(document).on('click', '[data-history-row]', function () {
        openHistory($(this).attr('id'))
    })

    $(document).on('change', '#history-filter input[name="date_from"]', function () {
        filter(true)
    })

    $('.h-screen').on('scroll', function() {
        
        if (isEndOfTable('#history-items [data-history-row]')) {
            filter()
        }
    })

    $(document).on('submit', '#save-history-form', function() {
        saveHistory(this)
    })
})

function isEndOfTable(tableRowSelector) {
    // Получаем элементы таблицы
    var tableRows = document.querySelectorAll(tableRowSelector);
    // Проверяем, достиг ли скролл предпоследней строки
    var secondLastRow = tableRows[tableRows.length - 2];
    return secondLastRow.getBoundingClientRect().top <= window.innerHeight;
}

window.openDeleteHistoryModal = (id) => {
    $('#delete-history-button').off('click').on('click', function() {
        deleteHistory(id)
    })
    $('[data-remodal-id="delete-history"]').remodal().open()
}


let limit = 30;
let offset = 0;
let isPending = false;
const filter = (isReset = false) => {

    if(isPending) {
        return false
    }

    isPending = true

    let form = document.getElementById('history-filter')

    let data = Object.fromEntries(new FormData(form))



    if(isReset) {
       offset = 0
    } else {
        offset = $('#history-items [data-history-row]').length
    }

    data.limit = limit
    data.offset = offset
    axios.post(
        API_HISTORY_LIST_URL,
        data
    ).then(r => {
        let mount = r.data.total_sum;
        

        if(isReset) {
            $('#history-items').html('')
            $('.saldo').html(parseFloat(r.data.sum).toFixed(1))
        }

        if(!r.data.histories.length) {
            if(!$('#history-items [data-history-row]').length) {
                (_.template($('#empty-template').html()))()
            }
        }

        r.data.histories.map(h => {
            console.log(mount)
            mount = Number(mount)
            $('#history-items').append(
                (_.template($('#history-item-template').html()))({
                    ...h,
                    mount,
                })
            )
            mount = parseFloat((mount - (h.count ?? 0))).toFixed(1)
        })
    }).finally(() => {
        setTimeout(() => {
            isPending = false
        }, 500);
    })
}
var sending = false;
const addHistory = (form) => {
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
        API_HISTORY_ADD,
        data
    ).then(r => {
        
        filter(true)
        let $modal = $(form).closest('.remodal')
        $modal.remodal().close()
        $modal.find('input[name="count"]').val('')

        $(form).find(`.errors`).addClass('hidden').html('')
        $(form).find('.input_error').html('')

        notify(
            "Движение создано",
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
        filter(true)
        let $modal = $(form).closest('.remodal')
        $modal.remodal().close()

        $(form).find(`.errors`).addClass('hidden').html('')
        $(form).find('.input_error').html('')

        notify(
            "Движение обновлено",
            2000,
        )
    }).catch(r => {
        console.log(r)
        if(r.response.status == 422) {
            for (const k in r.response.data.errors) {
                const e = r.response.data.errors[k];
                
                let html = '';
                e.map(m => {
                    html += m + '<br>'
                })
                $(form).find(`input[name="${k}"]`).siblings('.input_error').html(html)
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
        $('.remodal-history-update').remodal().close()
        $('[data-remodal-id="delete-history"]').remodal().close()

        notify(
            "Движение удалено",
            2000,
        )
    })
}

window.downloadExcel = () => {
    let data = {
        date_from: $('#filter-date').val(),
        position_id: $('#history-filter input[name="position_id"]').val(),
    }
    axios.post(
        API_HISTORY_EXCEL_URL,
        data
    ).then(r => {
        // Создаем ссылку на файл
        const url = window.URL.createObjectURL(new Blob([r.data]));
        // Создаем ссылку для скачивания файла
        const link = document.createElement('a');
        link.href = url;
        // Устанавливаем имя файла
        link.setAttribute('href', '/storage/history.xlsx');
        link.setAttribute('download', true);
        // Добавляем ссылку на страницу и кликаем по ней для начала загрузки
        document.body.appendChild(link);
        link.click();
        // Освобождаем ресурсы
        window.URL.revokeObjectURL(url);
    })
}