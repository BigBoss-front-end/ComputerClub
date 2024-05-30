import { CREATE_CLIENT_URL, DELETE_CLIENT_URL, GET_CLIENT_LIST_URL, GET_CLIENT_URL, UPDATE_CLIENT_URL } from "../../../utils/constants"
import { formSubmit } from "../../../utils/functions"
import { $rest } from "../../../utils/http"

$(function() {
    $(document).on('submit', '#add-client-form', function() {
        createClient(this)
    })
})

const createClient = (form) => {
    let data = new FormData(form)
    
    formSubmit(form, async () => {
        return await $rest.post(
            CREATE_CLIENT_URL,
            data,
        ).then(r => {
            console.log(r)
        }).finally(r => {
            console.log(r)
        })
    })
}

window.openClientEditModal = (id) => {
    getClient(id).then(client => {
        console.log(client)
        let $modal = $('[data-remodal-id="client-edit"]')
        let template = (_.template($('#computer-edit-modal-template').html()))({client})
        $modal.find('[data-modal-content]').html(template)
        $modal.remodal().open()
    })
}

window.saveClient = (form) => {
    let data = new FormData(form)
    form.reset = false
    formSubmit(form, async () => {
        return await $rest.post(
            UPDATE_CLIENT_URL,
            data,
        ).then(r => {
            console.log(r)
        }).finally(r => {
            console.log(r)
        })
    })
}

window.deleteClient = (id) => {
    return $rest.post(
        DELETE_CLIENT_URL,
        {id}
    ).then(r => {
        $(`[data-client-card][data-id="${id}"]`).remove()
        $('[data-remodal-id="client-edit"]').remodal().close()
    })
}

const getClient = async (id) => {
    return await axios.post(
        GET_CLIENT_URL,
        {id}
    ).then(r => {
        return r.data.client
    })
}
