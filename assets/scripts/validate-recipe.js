
//Function to validate data in form
$('#recipeForm').submit(function(){


    //Gets the data from the form and puts it into array
    var form = $('#recipeForm').serializeArray();


    //console.log(form);

    //Sets boolean var to know if its valid or not.
    var valid = true;

    //Array to add if not valid
    var invalidInputs = [];

    //Loops through each form value
    for(var i = 0; i < form.length; i++) {

        //If not valid
        if(!validate(form[i])) {
            //adds to array the ones that aren't valid
            invalidInputs.push("<li>Invalid input " + form[i].value + " for " + form[i].name + "!</li>");

            //sets valid to false
            valid = false;
        }

    }

    //If array isn't empty, there are errors so,
    if(invalidInputs.length != 0) {

        $('#invalid-list').text("");
         //for each value in the array, append to div in html
         invalidInputs.forEach(function(value) {
            $('#invalid-list').append(value);
         });

         //Make div shown so the user knows.
         $('#invalid-alert').removeClass('d-none');


    }

    //console.log(valid);

    //return true or false based on valid or not.
    return valid;
});


    //Switch to decide which validate to run
    function validate(input){
        switch(input.name) {

            //Validates name is number and not empty
            case 'prepTime':
                return validateNum(input.value);

            //Validates name is number and not empty
            case 'cookTime':
                return validateNum(input.value);

            //Validates name is number and not empty
            case 'servs':
                return validateNum(input.value);

            //Validates name is number and not empty
            case 'cals':
                return validateNum(input.value);

            //Validates name is 255 char or less and not empty
            case 'description':
                return validateTinyText(input.value);

            //Validates name is 255 char or less and not empty
            case 'ingreds[]':
                return validateTinyText(input.value);

            //Validates name is 255 char or less and not empty
            case 'directs[]':
                return validateTinyText(input.value);

            //Validates name is 255 char or less and not empty
            default:
                console.log(input.name);
                return false;
        }



    }

    //Validates num is not empty and a number
    function validateNum(num){

        if(num == "" ) {
            return false;
        }

        if(!isNaN(num)){

            //console.log('Is Number');
            return true;

        }

        return false;

    }


    //Validates descript is not empty and is less than 255 chars
    function validateTinyText(descript){

        if(descript == "") {
            return false;
        }

        if(descript.length < 255){

            //console.log("Descript is short enough");
            return true;

        }

        return false;
    }

