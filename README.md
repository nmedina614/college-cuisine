# college-cuisine
A website for college students who don't always want to eat ramen and want something better.


# Requirements:
* Separate all database/business logic:
    * Database Logic is kept in the model, 
      Buisness Logic split into Validation Class
      And assets/scripts
    
* Routes are all using Fat-Free:
    * Cannot get to any web page directly, only from route, check index.php

* Has Clearly Defined Database Layer using PDO
    * Model class used for Database Layer and PDO statements
    
* Data can be viewed, added, updated, and deleted
    * User can view recipes, add recipes, Like or Dislike the Recipe for updating
    and delete recipes, also add users and change the privilege of the user.
    
* History of Commits by both team members
    * 90+ commits on Github from Both members
    
* OOP with inheritance :
    * Moderator class inherits variables from User Class and adds to it
    
* Full DocBlocks on PHP files
    * DocBlock on all php files
    
* Validation is both Client side and Server side
    * Validation class handles validation for php, recipe-scripts.js
    for handling client side validation.
    
* Incorporates Jquery and Ajax
    * On recipe page, uses Ajax to verify if the user wants to delete the recipe.