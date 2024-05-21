import { CREATE_CLIENT_URL } from "../../../utils/constants"
import { formSubmit } from "../../../utils/functions"

$(function() {
    $(document).on('submit', '#add-client-form', function() {
        createClient(this)
    })
})

const createClient = (form) => {
    let data = new FormData(form)
    formSubmit(form, async () => {
        return await axios.post(
            CREATE_CLIENT_URL,
            data,
        ).then(r => {
            console.log(r)
        }).finally(r => {
            console.log(r)
        })
    })
}