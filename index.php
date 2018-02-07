<?php
/**
 * User: Aaron Melhaff
*/

// Require f3
require_once('vendor/autoload.php');

$f3 = Base::instance();

$f3->route('GET /', function($f3) {


    $view = new View;
    echo $view->render('pages/base.html');
});

$f3->run();