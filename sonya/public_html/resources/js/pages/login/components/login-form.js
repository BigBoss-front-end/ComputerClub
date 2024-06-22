import axios from "axios"
import { LOGIN_URL } from "../../../utils/constants"
import { formSubmit } from "../../../utils/functions"

$(function () {
    $(document).on('submit', '#login-form', function () {
        login(this)
    })
})

function login(form) {
    let data = new FormData(form)
    formSubmit(form, async () => {
        return await axios.post(
            LOGIN_URL,
            data,
        ).then(r => {
            location.href = '/'
        }).finally(r => {
            console.log(r)
        })
    })
}