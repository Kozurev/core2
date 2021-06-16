$(function() {

    initAjaxForm('#posts_ajax_form', function (response) {
        ajaxFormSuccessCallbackDefault(response, function() {
            window.location.reload();
        });
    });

    tinymce.init({
        selector: '#contentInput',
        plugins: 'advlist autolink lists link charmap print preview hr anchor pagebreak table',
        autosave_interval: "1s",
        toolbar_mode: 'floating',
    });

    $(document)
        .on('click', '.createPostBtn', function (e) {
            e.preventDefault();
            let $modal = $('#postsModal'),
                $titleInput = $('#titleInput'),
                $contentInput = $('#contentInput');
            $titleInput.val('');
            $contentInput.val('');
            tinymce.get('contentInput').setContent('');
            $modal.modal();
        });
});