import axios from "axios";
import { API_POSITION_ADD_URL, API_POSITION_DELETE_URL, API_POSITION_RESTORE_URL, API_POSITION_UPDATE_BATCH_URL } from './../../utils/constants';

// Complete SortableJS (with all plugins)
import Sortable from 'sortablejs/modular/sortable.complete.esm.js';
import { notify, sendingButton, sortElements } from "../../utils/functions";

$(function() {
    Sortable.create(
        document.getElementById('current-position-items'),
        {
            handle: ".handle",  // Drag handle selector within list items
            onUpdate: function (/**Event*/evt) {
                // same properties as onEnd
                evt.target.querySelectorAll('[data-position]').forEach(function(el, i) {
                    let value = (i+1) * 10;
                    el.setAttribute('data-sort', value)
                });
            },
        }
    );

    $(document).on('submit', 'form#add-position', function() {
        addPosition(this)
    })
})
var sending = false;
window.saveChanges = () => {
    let data = [];
    document.querySelectorAll('#position-items [data-position]').forEach(function(el, i) {
        data.push({
            id: el.getAttribute('data-id'),
            sort: el.getAttribute('data-sort'),
            name: el.querySelector('input[name="name"]').value
        })
    })

    if(sending) {
        return false
    }
    sending = true;
    sendingButton(document.querySelector('#save-changes'))

    axios.post(
        API_POSITION_UPDATE_BATCH_URL,
        {data: data}
    ).then(r => {
        notify(
            "Изменения сохранены",
            2000,
        )
    }).finally(() => {
        sendingButton(document.querySelector('#save-changes'), true)
        sending = false
    })
}

window.addPosition = (form) => {
    let data = Object.fromEntries(new FormData(form));

    if(sending) {
        return false
    }
    sending = true;
    sendingButton(form.querySelector('.sending_button'))
    
    axios.post(
        API_POSITION_ADD_URL,
        data,
    ).then(r => {
        $('#current-position-items').append(
            (_.template($('#position-row-template').html()))(r.data.position)
        )

        sortElements('#current-position-items [data-position]')

        form.reset()
        $('[data-remodal-id="add-position"]').remodal().close()

        notify(
            "Позиция создана",
            2000,
        )
    }).finally(() => {
        sendingButton(form.querySelector('.sending_button'), true)
        sending = false
    })
}

window.openDeletePositionModal = (id) => {
    $('#delete-position-button').off('click').on('click', function() {
        deletePosition(id)
    })
    $('[data-remodal-id="delete-position"]').remodal().open()
}

window.deletePosition = (id) => {
    axios.delete(
        API_POSITION_DELETE_URL(id),
    ).then(r => {
        $(`[data-position][data-id="${id}"]`).remove()
        $('#deleted-position-items').append(
            (_.template($('#position-row-template').html()))(r.data.position)
        )

        sortElements('#deleted-position-items [data-position]')

        $('[data-remodal-id="delete-position"]').remodal().close()

        notify(
            "Позиция удалена",
            2000,
        )
    })
}

window.restorePosition = (id) => {
    axios.post(
        API_POSITION_RESTORE_URL(id),
    ).then(r => {
        $(`[data-position][data-id="${id}"]`).remove()
        $('#current-position-items').append(
            (_.template($('#position-row-template').html()))(r.data.position)
        )

        sortElements('#current-position-items [data-position]')

        notify(
            "Позиция восстановлена",
            2000,
        )
    })
}