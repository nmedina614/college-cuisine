// TODO
$(document).ready(function() {
    $('#example').DataTable();
} );

// TODO
$('button.btn-reset-password').click(function() {
    const source = $('[data-source]').data('source');

    let confirmed = confirm("Are you sure you want to reset this users password?");
    if(confirmed) {
       console.log(this);
       let row = $(this).parent().parent();

       console.log(source);
       $.ajax({
           method: "POST",
           url: "//" + source + "/model/scripts/reset-password.php",
           data: generateUser(row),
           dataType: 'text',
           success: function(response) {
               //alert(response);
           }

       });

    }


});


$('button.btn-ban-user').click(function() {
    const source = $('[data-source]').data('source');

    let confirmed = confirm("Are you sure you want to ban this user?");
    if(confirmed) {
        console.log(this);
        let row = $(this).parent().parent();

        console.log(source);
        $.ajax({
            method: "POST",
            url: "//" + source + "/model/scripts/deactivate-user.php",
            data: generateUser(row),
            dataType: 'text',
            success: function(response) {
                //alert(response);
                location.reload();
            }



        });

    }


});

$('button.btn-delete-user').click(function() {
    const source = $('[data-source]').data('source');

    let confirmed = confirm("Are you sure you want to delete this user?");
    if(confirmed) {
        console.log(this);
        let row = $(this).parent().parent();

        console.log(source);
        $.ajax({
            method: "POST",
            url: "//" + source + "/model/scripts/delete-user.php",
            data: generateUser(row),
            dataType: 'text',
            success: function(response) {
                //alert(response);
                location.reload();
            }



        });

    }


});

$('button.btn-reinstate-user').click(function() {
    const source = $('[data-source]').data('source');

    let confirmed = confirm("Are you sure you want to reinstate this user?");
    if(confirmed) {
        console.log(this);
        let row = $(this).parent().parent();

        console.log(source);
        $.ajax({
            method: "POST",
            url: "//" + source + "/model/scripts/reinstate-user.php",
            data: generateUser(row),
            dataType: 'text',
            success: function(response) {
                //alert(response);
                location.reload();
            }



        });

    }


});

$('button.btn-promote-user').click(function() {
    const source = $('[data-source]').data('source');

    let confirmed = confirm("Are you sure you want to promote this user?");
    if(confirmed) {
        console.log(this);
        let row = $(this).parent().parent();

        console.log(source);
        $.ajax({
            method: "POST",
            url: "//" + source + "/model/scripts/promote-user.php",
            data: generateUser(row),
            dataType: 'text',
            success: function(response) {
                //alert(response);
                location.reload();
            }



        });

    }


});

$('button.btn-demote-user').click(function() {
    const source = $('[data-source]').data('source');

    let confirmed = confirm("Are you sure you want to demote this user?");
    if(confirmed) {
        console.log(this);
        let row = $(this).parent().parent();

        console.log(source);
        $.ajax({
            method: "POST",
            url: "//" + source + "/model/scripts/demote-user.php",
            data: generateUser(row),
            dataType: 'text',
            success: function(response) {
                //alert(response);
                location.reload();
            }



        });

    }


});

/**
 * TODO
 *
 * @param row
 * @returns {{userid: number | * | T | jQuery, username: number | * | T | jQuery, email: number | * | T | jQuery, privilege: number | * | T | jQuery}}
 */
function generateUser(row) {

    console.log(row);

    let output = {
        userid : $(row).find('[data-type="userid"]').data('value'),
        username : $(row).find('[data-type="username"]').data('value'),
        email : $(row).find('[data-type="email"]').data('value'),
        privilege : $(row).find('[data-type="privilege"]').data('value')
    }

    console.log(output);

    return output;


}