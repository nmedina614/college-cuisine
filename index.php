<?php
/**
 * User: Aaron Melhaff
*/

// Require f3
require_once('vendor/autoload.php');

$f3 = Base::instance();

$f3->route('GET /', function($f3) {
    $f3->set('title', 'College Cuisine');
    $f3->set('content', 'views/home.html');

    $template = new Template();
    echo $template->render('views/base.html');
});

$f3->run();