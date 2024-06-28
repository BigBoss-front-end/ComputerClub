import axios from "axios"
import { API_USER_ADD_URL, API_USER_DELETE_URL, API_USER_UPDATE_URL, API_USER_URL } from "../../utils/constants"
import { notify, sendingButton, sortElements } from "../../utils/functions"
import { forIn } from "lodash"

var sending = false;
window.addUser = (form) => {
    let data = new FormData(form)

    if(sending) {
        return false
    }
    sending = true;
    sendingButton(form.querySelector('.sending_button'))

    axios.post(
        API_USER_ADD_URL,
        data,
    ).then(r => {
        $('#user-items').append(
            (_.template($('#user-template').html()))(r.data.user)
        )
        form.reset()
        $('[data-remodal-id="add-user"]').remodal().close()

        $(form).find('.input_error').html('')

        notify(
            "Пользователь создан",
            2000,
        )
    }).catch(r => {
        for (const k in r.response.data.errors) {
            const e = r.response.data.errors[k];
            
            let html = '';
            e.map(m => {
                html += m + '<br>'
            })
            $(form).find(`input[name="${k}"]`).siblings('.input_error').html(html)
        }
    }).finally(() => {
        sendingButton(document.querySelector('.sending_button'), true)
        sending = false
    })
}

window.userModal = (id) => {
    axios.get(
        API_USER_URL(id)
    ).then(r => {
        let $modal = $('[data-remodal-id="update-user"]')
        $modal.html(
            (_.template($('#user-modal-template').html()))(r.data.user)
        )
        $modal.find('select[name="role_id"] option').each(function(i, o) {
            if($(o).attr('value') == r.data.user.role_id) {
                $(o).attr('selected', true)
            }
        })
        $modal.remodal().open()
    })
}

window.blockUser = (id) => {
    let data = {
        id: id,
        is_blocked: 1,
    }

    axios.post(
        API_USER_UPDATE_URL(id),
        data,
    ).then(r => {
        $(`[data-user][data-id="${r.data.user.id}"]`).replaceWith(
            (_.template($('#user-template').html()))(r.data.user)
        )
        notify(
            "Пользователь заблокирован",
            2000,
        )
    })
}

window.unBlockUser = (id) => {
    let data = {
        id: id,
        is_blocked: 0,
    }

    axios.post(
        API_USER_UPDATE_URL(id),
        data,
    ).then(r => {
        $(`[data-user][data-id="${r.data.user.id}"]`).replaceWith(
            (_.template($('#user-template').html()))(r.data.user)
        )
        notify(
            "Пользователь разблокирован",
            2000,
        )
    })
}


window.updateUser = (form) => {
    let data = new FormData(form)

    if(sending) {
        return false
    }
    sending = true;
    sendingButton(form.querySelector('.sending_button'))

    axios.post(
        API_USER_UPDATE_URL(data.get('id')),
        data,
    ).then(r => {
        $(`[data-user][data-id="${r.data.user.id}"]`).replaceWith(
            (_.template($('#user-template').html()))(r.data.user)
        )
        form.reset()
        $('[data-remodal-id="update-user"]').remodal().close()

        $(form).find('.inpur_error').html('')

        notify(
            "Пользователь обновлен",
            2000,
        )
    }).catch(r => {
        for (const k in r.response.data.errors) {
            const e = r.response.data.errors[k];
            
            let html = '';
            e.map(m => {
                html += m + '<br>'
            })
            $(form).find(`input[name="${k}"]`).siblings('.input_error').html(html)
        }
    }).finally(() => {
        sendingButton(document.querySelector('.sending_button'), true)
        sending = false
    })
}

window.openDeleteUserModal = (id) => {
    $('#delete-user-button').off('click').on('click', function() {
        deleteUser(id)
    })
    $('[data-remodal-id="delete-user"]').remodal().open()
}

const deleteUser = (id) => {
    axios.delete(
        API_USER_DELETE_URL(id),
    ).then(r => {
        $(`[data-user][data-id="${id}"]`).remove()

        $('[data-remodal-id="delete-user"]').remodal().close()

        notify(
            "Пользователь удален",
            2000,
        )
    })
}