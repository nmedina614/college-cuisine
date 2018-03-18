<?php
/**
 * Created by PhpStorm.
 * User: nash
 * Date: 3/17/2018
 * Time: 1:05 AM
 */

class Validator
{
    /**
     * TODO
     *
     * @returns
     */
    public static function validateRegistration($username, $password1, $password2, $email)
    {
        $invalid = array();


        if(preg_match("/^[0-9a-zA-Z_]{8,}$/", $_POST["user"])) {
            $invalid[] = 'User must be bigger that 8 chars and contain only digits, letters and underscore';
        }

        if(!empty($password1)) {

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
            if(!preg_match("/[$@$!%*#?&]+/",$password1)) {

                $invalid[] = "Your Password Must Contain At Least 1 special character!";

            }
        }
        elseif($password1 != $password2) {
            $invalid[] = "Passwords do not match!";
        } else {
            $invalid[] = "Please enter password";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $invalid[] = "Invalid email address!";
        }

        return $invalid;
    }

    public static function test_input($data) {
        return $data;
    }


    /**
     * TODO
     */
    public static function validateRecipe()
    {
        $errors = array('There was an error in your submit recipe form:');
        //echo sizeof($errors);
        foreach($_POST as $value){
            $valid = self::notEmpty($value);
            $error = "You are missing data, Please make sure all fields are not empty";
            if(!$valid){
                array_push($errors, $error);
                break;
            }
        }
        $valid = self::isAlphaNum($_POST['recipeName']);
        if(!$valid){
            array_push($errors, "There is an error in the Recipe Name, Please keep the name alphanumeric");
        }
        $valid = self::validateNum($_POST['prepTime']);
        if(!$valid){
            array_push($errors, "There is an error in the Prep Time, please keep it a positive number");
        }
        $valid = self::validateNum($_POST['cookTime']);
        if(!$valid){
            array_push($errors, "There is an error in the Cook Time, please keep it a positive number");
        }
        $valid = self::validateNum($_POST['servs']);
        if(!$valid){
            array_push($errors, "There is an error in the Servings, please keep it a positive number");
        }
        $valid = self::validateNum($_POST['cals']);
        if(!$valid){
            array_push($errors, "There is an error in the Calories, please keep it a positive number");
        }
        $valid = self::validateTinyText($_POST['description']);
        if(!$valid){
            array_push($errors, "There is an error in the description, please try to make it under 255 characters");
        }
        foreach($_POST['ingreds'] as $value){
            $valid = self::validateTinyText($value);
            if(!$valid){
                array_push($errors, "There is an error in the Ingredients, 
                please keep under 255 characters per ingredient");
                break;
            }
        }
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

        //Target File to upload
        try{
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
        }catch(Exception $e){
            $GLOBALS['target_file'] = 'assets/images/default.jpg';
        }

        //echo sizeof($errors);
        if(sizeof($errors)==1){
            $errors = null;
        }
        return $errors;
    }

    /**
     * @param $data - value to test Validation function
     */
    public static function notEmpty($data)
    {
        if ($_POST['recipeName'] == "") {
            return false;
        }
        return true;
    }
    public static function isAlphaNum($data)
    {
        if (!preg_match('/^[a-z\d\-_\s]+$/i', $data)) {
            return false;
        }
        return true;
    }
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
    public static function validateTinyText($data)
    {
        if(!(strlen($data) < 255)){
            return false;
        }
        return true;
    }


    public static function validateLike($userID, $recipeID){



        $sql = 'SELECT * FROM `liked-recipes` WHERE userID = :userID AND recipeID = :recipeID';

        $searchQuery = self::$_dbh->prepare($sql);

        $searchQuery->bindParam(':userID', $userID, PDO::PARAM_STR);
        $searchQuery->bindParam(':recipeID', $recipeID, PDO::PARAM_STR);

        $searchQuery->execute();

        $result = $searchQuery->rowCount();

        if($result > 0){

            return false;

        } else {
            return true;
        }

    }

    public static function validateDislike($userID, $recipeID){

        $sql = 'SELECT * FROM `dislike-recipe` WHERE userID = :userID AND recipeID = :recipeID';

        $searchQuery = self::$_dbh->prepare($sql);

        $searchQuery->bindParam(':userID', $userID, PDO::PARAM_STR);
        $searchQuery->bindParam(':recipeID', $recipeID, PDO::PARAM_STR);

        $searchQuery->execute();

        $result = $searchQuery->rowCount();

        if($result > 0){

            return false;

        } else {
            return true;
        }

    }

}