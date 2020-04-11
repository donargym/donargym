$('.remove-news-post').click(function () {
    let title = $(this).data('title');
    let id = $(this).data('id');
    let modal = $('#remove-news-post-modal');
    modal.find('.modal-body #item-title').text(title);
    modal.find('.modal-body #id').val(id);
    modal.modal('show');
});
