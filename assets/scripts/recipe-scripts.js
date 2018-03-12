$('.btn-ingredient-delete').click(function() {
    var parent = this.parentElement;
    $(parent).remove();
});

$('#btn-add-ingredient').click(function() {
    const CLONE_HTML =
        '<div class="input-group mb-3 ingredient-item">\n' +
        '    <input type="text" class="form-control" placeholder="Add Ingredients Here" class="input-ingredient">\n' +
        '    <button class="btn btn-danger btn-ingredient-delete" type="button"><img class="icon-delete" src="../assets/images/icons/close.svg" alt="Remove"></button>\n' +
        '</div>';

    console.log(CLONE_HTML);
    $('#ingredient-list').append(CLONE_HTML);

    // Add event listener to new node.
    $('.btn-ingredient-delete').click(function() {
        var parent = this.parentElement;
        $(parent).remove();
    });
});

