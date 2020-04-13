$('.remove-uploaded-file').click(function () {
    let name = $(this).data('name');
    let id = $(this).data('id');
    let modal = $('#remove-uploaded-file-modal');
    modal.find('.modal-body #item-name').text(name);
    modal.find('.modal-body #id').val(id);
    modal.modal('show');
});
