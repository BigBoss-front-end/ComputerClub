

export const openModal = ($modal) => {
    $modal.removeClass('closing').addClass('opening')
}

export const closeModal = ($modal) => {
    if($modal.hasClass('opening')) {
        $modal.removeClass('opening').addClass('closing')
    }
}