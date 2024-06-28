import axios from "axios";
import { countBy } from "lodash";
import { API_REVISION_BATCH } from "../../utils/constants";
import { notify, sendingButton } from "../../utils/functions";

var sending = false;
window.saveRevisions = () => {
    let data = [];

    let isError = false
    $('[data-position]').each(function(i, p) {
        let count = $(p).find('input[name="count"]').val()
        if(count < 0) {
            $(p).find('.input_error').html('Значение не может быть меньше 0')
            isError = true;
        }
        data.push({
            id: $(p).attr('data-id'),
            count: $(p).find('input[name="count"]').val()
        })
    })

    if(isError) {
        return false
    }

    if(sending) {
        return false
    }
    sending = true;
    sendingButton(document.querySelector('#save-revisions'))

    axios.post(
        API_REVISION_BATCH,
        {data: data}
    ).then(r => {
        notify(
            "Изменения сохранены",
            2000,
        )
        $('.input_error').html('')
    }).finally(() => {
        sendingButton(document.querySelector('#save-revisions'), true)
        sending = false
    })
}