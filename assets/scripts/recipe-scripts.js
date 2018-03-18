$('.btn-ingredient-delete').click(function() {
    var parent = this.parentElement;
    $(parent).remove();
});

$('#btn-add-ingredient').click(function() {
    const CLONE_HTML =
        '<div class="input-group mb-3 ingredient-item">\n' +
        '    <input type="text" class="form-control" placeholder="Add Ingredients Here" class="input-ingredient" name="ingreds[]">\n' +
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

$('.btn-direct-delete').click(function() {
    var parent = this.parentElement;
    $(parent).remove();
});

$('#btn-add-direct').click(function() {
    const CLONE_HTML =
        '<div class="input-group mb-3 direct-item">\n' +
        '    <input type="text" class="form-control" placeholder="Add Directions Here" class="input-directions" name="directs[]">\n' +
        '    <button class="btn btn-danger btn-direct-delete" type="button"><img class="icon-delete" src="../assets/images/icons/close.svg" alt="Remove"></button>\n' +
        '</div>';

    console.log(CLONE_HTML);
    $('#direction-list').append(CLONE_HTML);

    // Add event listener to new node.
    $('.btn-direct-delete').click(function() {
        var parent = this.parentElement;
        $(parent).remove();
    });
});


$('button.btn-delete-recipe').click(function() {
    const source = $('[data-source]').data('source');

    var confirmed = confirm("Are you sure you want to delete this user?");
    if(confirmed) {

        console.log(source);
        $.ajax({
            method: "POST",
            url: "//" + source + "/model/scripts/delete-recipe.php",
            dataType: 'text',
            success: function (response) {
                alert(response);
                if(response ==='Unauthorized'){
                    location.reload();
                } else window.location.replace("//" + source + "/");
            }

        });

    }
});
