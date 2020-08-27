<?php
//Include classes
include_once $_SERVER['DOCUMENT_ROOT'].'/objects/Database.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/objects/Request.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/objects/Router.php';
include_once $_SERVER['DOCUMENT_ROOT'].'/objects/Translator.php';

//Include Environnement file
include_once $_SERVER['DOCUMENT_ROOT'].'/env.php';

//Instanciate objects
$db = new Database();
$translator = new Translator();
$router = new Router(new Request, $db, $auth);


//Prevent acces from file name
$router->get('/index.php', function() {
  include_once $_SERVER['DOCUMENT_ROOT'].'/errors/403.html';
  die();
});

$router->get('/test', function() {
  setTranslation("TEST.1","salut","fr");
  setTranslation("TEST.1","hello","en");
  setTranslation("TEST.2","911 is known in any languages");
  
});

