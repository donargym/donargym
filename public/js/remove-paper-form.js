$('.remove-paper-form').click(function () {
    let label = $(this).data('label');
    let id = $(this).data('id');
    let modal = $('#remove-paper-form-modal');
    modal.find('.modal-body #item-label').text(label);
    modal.find('.modal-body #id').val(id);
    modal.modal('show');
});
