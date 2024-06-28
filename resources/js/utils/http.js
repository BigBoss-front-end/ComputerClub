import axios from "axios";
import iziToast from 'izitoast';

export const $instanse = axios.create()



export const $rest = axios.create()

$rest.interceptors.response.use(response => {
    iziToast.success({
        title: 'Успешно',
    });

    return response;
}, error => {
    if(error.response.status == 422) {
        iziToast.warning({
            title: 'Внимание',
            message: 'Ошибка валидации'
        });
    } else if(error.response.status == 500) {
        iziToast.error({
            title: 'Ошибка',
            message: 'Ошибка сервера'
        });
    } else {
        iziToast.error({
            title: 'Ошибка',
            message: error.response.data.message
        });
    }

    return Promise.reject(error);
})