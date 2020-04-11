$('.remove-frequently-asked-question').click(function () {
    let question = $(this).data('question');
    let id = $(this).data('id');
    let modal = $('#remove-frequently-asked-question-modal');
    modal.find('.modal-body #item-question').text(question);
    modal.find('.modal-body #id').val(id);
    modal.modal('show');
});
