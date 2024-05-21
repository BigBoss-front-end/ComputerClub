export const formSubmit = (form, promise) => {
    // Получаем id формы
    let formId = form.getAttribute('data-form-id')

    // Если форма уже отправляется, блокируем повторную отправку
    if (window[`form-${formId}`]) {
        return false
    }

    // Форматируем кнопку
    let button = form.querySelector('[data-submit-btn]')
    formProccessButton(button)

    window[`form-${formId}`] = true
    formValidError(form, {}, true)
    return promise()
        .then(() => {
            // Очищаем форму
            console.log(form.reset)
            if(form.reset) {
                form.reset()
            }
        }).catch(e => {
            console.log(e)
            // Показываем ошибку валидации
            if(e.response.status == 422) {
                formValidError(form, e.response.data.errors)
            }
        }).finally(() => {
            // возвращаем в исходное состояние
            formProccessButton(button, true)
            window[`form-${formId}`] = false
    }   )
}

export const formProccessButton = (button, reset = false) => {
    let id = button.getAttribute('data-btn-id');
    if (!window[`btn-${id}`]) {
        window[`btn-${id}`] = button.innerHTML
    }
    if (!reset) {
        button.classList.add('processing')
        button.innerHTML = 'Загрузка...'
    } else {
        button.classList.remove('processing')
        button.innerHTML = window[`btn-${id}`]
    }
}

export const formValidError = (form, errors = {}, reset = false) => {
    if(reset) {
        form.querySelectorAll('[data-form-error]').forEach(input => {
            input.innerHTML = ''
        })
        return;
    }
    for (const key in errors) {
        let error = errors[key]
        let input = form.querySelector(`[data-form-error][data-name="${key}"]`)
        if(input) {
            input.innerHTML = typeof error == 'string' ? error : error[0]
        }
    }
}