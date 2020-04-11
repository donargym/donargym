$('.remove-calendar-item').click(function () {
    let name = $(this).data('name');
    let id = $(this).data('id');
    let modal = $('#removePublicCalendarItem');
    modal.find('.modal-body #item-name').text(name);
    modal.find('.modal-body #id').val(id);
    modal.modal('show');
});
