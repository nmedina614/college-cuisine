

$('#recipeForm').submit(function(){

    var form = $('#recipeForm').serializeArray();


    console.log(form);

    var valid = true;
    var invalidInputs = [];

     for(var i = 0; i < form.length; i++) {
        if(!validate(form[i])) {
            invalidInputs.push("<li>Invalid input " + form[i].value + " for " + form[i].name + "!</li>");
            valid = false;
        }

     }

     if(invalidInputs.length != 0) {

         invalidInputs.forEach(function(value) {
            $('#invalid-list').append(value);
         });

         $('#invalid-alert').removeClass('d-none');


     }

     console.log(valid);

    return valid;
});

    function validate(input){
        switch(input.name) {
            case 'recipeName':
                return validateName(input.value);

            case 'prepTime':
                return validateNum(input.value);

            case 'cookTime':
                return validateNum(input.value);

            case 'servs':
                return validateNum(input.value);

            case 'cals':
                return validateNum(input.value);

            case 'description':
                return validateTinyText(input.value);

            case 'ingreds[]':
                return validateTinyText(input.value);

            case 'directs[]':
                return validateTinyText(input.value);

            default:
                console.log(input.name);
                return false;
        }



    }


    function validateName(name){

        if(name == "" ) {

            return false;
        }

        var letters = '/^[0-9a-zA-Z]+$/';
        if(name.match(letters)) {
            console.log('Name is Valid');
            return true;
        }
        return false;

    }

    function validateNum(num){

        if(num == "" ) {
            return false;
        }

        if(!isNaN(num)){

            console.log('Is Number');
            return true;

        }

        return false;

    }


    function validateTinyText(descript){

        if(descript == "") {
            return false;
        }

        if(descript.length < 255){

            console.log("Descript is short enough");
            return true;

        }

        return false;
    }

