$('.remove-club-magazine').click(function () {
    let date = $(this).data('date');
    let id = $(this).data('id');
    let modal = $('#remove-club-magazine-modal');
    console.log(modal);
    modal.find('.modal-body #item-date').text(date);
    modal.find('.modal-body #id').val(id);
    modal.modal('show');
});
