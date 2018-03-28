<?php

/**
 * Class Validator
 *
 * Class used to handle input validation.
 *
 * @author Aaron Melhaff <nash_melhaff@hotmail.com>
 * @author Nolan Medina <njmedina614@gmail.com>
 */
class Validator
{
    /**
     * Method used to validate a user registration attempt.
     *
     * @param $username String representing the username of the new user.
     * @param $password1 String representing the password of the new user.
     * @param $password2 String representing the repeated password of the new user.
     * @param $email String representing the email address of the new user.
     *
     * @returns array Returns an array of string containing failed test results.
     */
    public static function validateRegistration($username, $password1, $password2, $email)
    {
        $invalid = array();

        $invalidUsername = self::validateUsername($username);

        foreach($invalidUsername as $value) {
            $invalid[] = $value;
        }


        $invalidPassword = self::validatePassword($password1, $password2);

        foreach($invalidPassword as $value) {
            $invalid[] = $value;
        }

        if (!self::validateEmail($email)) {
            $invalid[] = "Invalid email address!";
        }

        return $invalid;
    }

    /**
     * Method for validating new passwords.
     *
     * @param $password1 String representing the first password entry.
     * @param $password2 String representing the repeat password entry.
     * @return array Returns an array of string containing failure messages.
     */
    public static function validatePassword($password1, $password2)
    {
        $invalid = array();

        if(!empty($password1)) {
            if($password1 != $password2) {

                $invalid[] = "Passwords do not match!";
            }
            if(strlen($password1) <= '8') {

                $invalid[] = "Your Password Must Contain At Least 8 Characters!";
            }
            if(!preg_match("#[0-9]+#",$password1)) {

                $invalid[] = "Your Password Must Contain At Least 1 Number!";
            }
            if(!preg_match("#[A-Z]+#",$password1)) {

                $invalid[] = "Your Password Must Contain At Least 1 Capital Letter!";
            }
            if(!preg_match("#[a-z]+#",$password1)) {

                $invalid[] = "Your Password Must Contain At Least 1 Lowercase Letter!";
            }
        } else {
            $invalid[] = "Please enter password";
        }

        return $invalid;
    }


    /**
     * Validates that the user can submit the form.
     *
     * Validates that the user can submit the form,
     * correctly else return the user to the page of the form
     * and have them enter the data correctly and also uploads the file
     * to the server, if none are chosen then uses a default file, or if there's
     * an error, reports it just a like the rest of the validation.
     *
     * @return array - Errors array to show errors in form.
     */
    public static function validateRecipe()
    {
        //Default Value to initialize array
        $errors = array('There was an error in your submit recipe form:');


        //Checks to see if the name is alphanumeric + space
        $valid = self::isAlphaNum($_POST['recipeName']);
        if(!$valid){
            //Pushes to array if invalid
            array_push($errors, "There is an error in the Recipe Name, Please keep the name alphanumeric");
        }

        //makes sure that the value is a number
        $valid = self::validateNum($_POST['prepTime']);
        if(!$valid){
            array_push($errors, "There is an error in the Prep Time, please keep it a positive number");
        }

        //makes sure that the value is a number
        $valid = self::validateNum($_POST['cookTime']);
        if(!$valid){
            array_push($errors, "There is an error in the Cook Time, please keep it a positive number");
        }

        //makes sure that the value is a number
        $valid = self::validateNum($_POST['servs']);
        if(!$valid){
            array_push($errors, "There is an error in the Servings, please keep it a positive number");
        }

        //makes sure that the value is less than 255 chars
        $valid = self::validateTinyText($_POST['description']);
        if(!$valid){
            array_push($errors, "There is an error in the description, please try to make it under 255 characters");
        }

        //Goes through ingreds array to make sure all are less than 255 chars
        foreach($_POST['ingreds'] as $value){
            $valid = self::validateTinyText($value);
            if(!$valid){
                array_push($errors, "There is an error in the Ingredients, 
                please keep under 255 characters per ingredient");
                break;
            }
        }

        //Goes through directs array to make sure all are less than 255 chars
        foreach($_POST['directs'] as $value){
            $valid = self::validateTinyText($value);
            if(!$valid){
                array_push($errors, "There is an error in the directions, 
                please keep under 255 characters per direction");
                break;
            }
        }

        //Target Directory for file upload
        $target_dir = "assets/images/";

        //Trys to upload file - taken from https://www.w3schools.com/php/php_file_upload.asp
        try{
            //gets the file path to where you want to upload the image to
            $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
            $uploadOk = 1;
            //Image file type
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
            // Check if image file is a actual image or fake image
            if(isset($_POST["submit"])) {
                $file = $_FILES["fileToUpload"]["tmp_name"];
                if($file==null){
                    $exception = "Exception";
                    throw new Exception($exception);
                }
                $check = getimagesize($file);
                if($check !== false) {
                    $uploadOk = 1;
                } else {
                    array_push($errors, "File is not an image.");
                    $uploadOk = 0;
                }
            }
            // Check file size
            if ($_FILES["fileToUpload"]["size"] > 500000) {
                array_push($errors,"Sorry, your file is too large.");
                $uploadOk = 0;
            }
            // Allow certain file formats
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
                && $imageFileType != "gif" ) {
                array_push($errors,"Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {
                array_push($errors, "Sorry, your file was not uploaded.");

                // if everything is ok, try to upload file
            } else {
                if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                    //echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
                    $GLOBALS['target_file'] = $target_file;
                } else {
                    array_push($errors, "Sorry, there was an error uploading your file.");
                }
            }
            //If there is a problem with uploading the file, uses default image.
        }catch(Exception $e){
            $GLOBALS['target_file'] = 'assets/images/default.jpg';
        }

        //if size of errors is one, no validation errors so return null array for
        //comparison later.
        if(sizeof($errors)==1){
            $errors = null;
        }

        //Returns errors array
        return $errors;
    }

    /**
     * Method that checks if a recipe name is empty.
     *
     * @param $value mixed checks to see if input is empty
     * @return boolean Returns true or false if empty.
     */
    public static function notEmpty($value)
    {
        if ($value == "") {
            return false;
        }
        return true;
    }

    /**
     * Method that returns whether the input is alphanumeric.
     *
     * @param $data INT Input being compared.
     * @return bool Boolean representing test success or failure
     */
    public static function isAlphaNum($data)
    {
        if (!preg_match('/^[a-z\d\-_\s]+$/i', $data)) {
            return false;
        }
        return true;
    }

    /**
     * Method that checks if input is numeric.
     *
     * @param $num INT Takes input
     * @return bool Returns boolean result.
     */
    public static function validateNum($num)
    {
        if (!is_numeric($num)) {
            return false;
        }
        if($num < 0){
            return false;
        }
        return true;
    }

    /**
     * Method for returning if the string is the proper
     * length for a tinytext value.
     *
     * @param $data String Input being evaluated.
     * @return bool Returns boolean test result.
     */
    public static function validateTinyText($data)
    {
        if(!(strlen($data) < 255)){
            return false;
        }
        return true;
    }

    /**
     * Method that checks if a username is valid.
     *
     * @param $username String username being validated.
     * @return array Returns an array of strings containing failed test results.
     */
    public static function validateUsername($username)
    {
        $invalid = array();

        if(!preg_match('/^[a-zA-Z]+[a-zA-Z0-9._]+$/', $username)) {
            $invalid[] = 'Username must be alphanumeric!';
        }
        if(strlen($username) <= 8) {
            $invalid[] = 'Username must be at least 8 characters long!';
        }
        if(strlen($username) > 40) {
            $invalid[] = 'Username cannot be longer than 40 characters long!';
        }

        return $invalid;
    }

    /**
     * Function for validating email addresses.
     *
     * @param $email String address being checked.
     * @return Returns true if input matches email format.
     */
    public static function validateEmail($email)
    {
        if(isset($email)) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }

        return false;
    }


}