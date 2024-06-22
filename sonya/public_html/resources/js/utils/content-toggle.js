$(function() {
    $(document).on('click', '[data-content-nav]', function() {
        let $wrapper = $(this).closest('[data-content-wrapper]')
        let id = $(this).attr('data-id')

        $wrapper.find('[data-content-nav]').not(this).removeClass('active')
        $(this).addClass('active')

        let $content = $wrapper.find(`[data-content][data-id="${id}"]`)
        $wrapper.find('[data-content]').not($content).hide()
        $content.show()
    })
})