import './bootstrap';
import { closeModal, openModal } from './utils/modal';

$(function() {
    
    $(document).on('click', '#menu-btn', function() {
        $(this).toggleClass('active')
    })

    $(document).on('submit', '.submit-prevent-default', function(e) {
        e.preventDefault()
    })


    $(function() {
        $(document).on('click', '[data-modal-target]', function() {
            let id = $(this).attr('data-modal-target')
            let $modal = $(`[data-modal][data-modal-id="${id}"]`)
            closeModal($(`[data-modal]`).not($modal))
            openModal($modal)
        })
    
        $(document).on('click', '[data-modal-close]', function() {
            closeModal($(this).closest('[data-modal]'))
        })
    })
})