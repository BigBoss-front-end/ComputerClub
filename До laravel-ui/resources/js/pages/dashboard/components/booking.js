import axios from "axios"
import { DELETE_BOOKING_URL } from "../../../utils/constants"

window.deleteBooking = (id) => {
    axios.post(
        DELETE_BOOKING_URL,
        {id}
    ).then(r => {
        $(`[data-booking][data-id="${id}"]`).remove()
    })
}