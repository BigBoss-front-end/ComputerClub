import axios from "axios"
import { API_HISTORY_EXCEL_URL, API_POSITIONS_URL } from "../../utils/constants"
import { data } from "jquery"

$(function () {
    $(document).on('change', '#filter-date', function () {
        filter(this)
    })

    filter()
})

const filter = (form) => {
    let data = new FormData()

    data.append('history_date_from', $('#filter-date').val())

    $('#load-btn').show()
    $('#total').hide()

    axios.postForm(
        API_POSITIONS_URL,
        data
    ).then(r => {
        console.log(r)
        if (r.data.positions.length) {
            $('#position-rows').html('')
            r.data.positions.map(position => {
                $('#position-rows').append(
                    (_.template($('#position-row').html()))(position)
                )
            })
            $('#total-count').html(Number(r.data.count).toFixed(1) + ' кг')
        }
    }).finally(() => {
        $('#load-btn').hide()
        $('#total').show()
    })
}

window.downloadExcel = () => {
    let data = {
        date_from: $('#filter-date').val()
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