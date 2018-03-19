
//button for delete an ingredient from the list of ingredients
//when submitting a new ingredient
$('.btn-ingredient-delete').click(function() {
    var parent = this.parentElement;
    $(parent).remove();
});

//Button for adding a ingredient to the list of ingredients
//when submitting a new ingredient
$('#btn-add-ingredient').click(function() {
    const CLONE_HTML =
        '<div class="input-group mb-3 ingredient-item">\n' +
        '    <input type="text" class="form-control" placeholder="Add Ingredients Here" class="input-ingredient" name="ingreds[]">\n' +
        '    <button class="btn btn-danger btn-ingredient-delete" type="button"><img class="icon-delete" src="../assets/images/icons/close.svg" alt="Remove"></button>\n' +
        '</div>';

    //console.log(CLONE_HTML);
    $('#ingredient-list').append(CLONE_HTML);

    // Add event listener to new node.
    $('.btn-ingredient-delete').click(function() {
        var parent = this.parentElement;
        $(parent).remove();
    });
});

//Remove a direction from the direction list
$('.btn-direct-delete').click(function() {
    var parent = this.parentElement;
    $(parent).remove();
});

//Adding a direction when submitting a form
$('#btn-add-direct').click(function() {
    const CLONE_HTML =
        '<div class="input-group mb-3 direct-item">\n' +
        '    <input type="text" class="form-control" placeholder="Add Directions Here" class="input-directions" name="directs[]">\n' +
        '    <button class="btn btn-danger btn-direct-delete" type="button"><img class="icon-delete" src="../assets/images/icons/close.svg" alt="Remove"></button>\n' +
        '</div>';

    //console.log(CLONE_HTML);
    $('#direction-list').append(CLONE_HTML);

    // Add event listener to new node.
    $('.btn-direct-delete').click(function() {
        var parent = this.parentElement;
        $(parent).remove();
    });
});

//Verify the user wants to delete the recipe and that the
//user has high enough privilege or the user that created the
//recipe
$('button.btn-delete-recipe').click(function() {

    //Source of page, used for same code on both websites
    //easier for github collab.
    const source = $('[data-source]').data('source');

    //Yes or No alert to confirm they want to delete the account
    var confirmed = confirm("Are you sure you want to delete this user?");

    //If yes, attempts to delete the recipe
    if(confirmed) {
        //console.log(source);
        //AJAX to use php
        $.ajax({
            method: "POST",

            //Page that verifys user
            url: "//" + source + "/model/scripts/delete-recipe.php",
            dataType: 'text',

            //gets response from php page
            success: function (response) {
                //Tells user if they deleted the recipe
                //or if they are unauthorized
                alert(response);

                //if user is unauthorized, won't rediret to home page
                if(response ==='Unauthorized'){
                    location.reload();

                    //Redirect back to home page if recipe is deleted.
                } else window.location.replace("//" + source + "/");
            }

        });

    }
});
