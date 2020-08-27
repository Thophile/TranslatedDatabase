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

/**
 * router define routes and associated callback, 
 * callback argument must be in the same order as specified in 
 * the router resolve() method's argument array
 */
$router->get('/test', function($request, $db) {

  //setting path "TEST.1" translation in two different languages
  setTranslation("TEST.1","bonjour","fr");
  setTranslation("TEST.1","hello","en");

  //setting path "TEST.2" translation for all languages
  setTranslation("TEST.2","911 is known in any languages");

  /**
   * setting a row inside the people table 
   * ID must be capital , it is used to check wheter the row must be created or updated
   * when creating a row it's id is returned
   * 
   * if the translator class is available the row is then sent to it and only the path is 
   * written in the database while the current data are saved in the current local 
   * translation file
  */
  $db->set("people",array(
    "ID" => 0,
    "name" => "bob ross",
    "description" => "is awesome"
  ));

  /**
   * getting a row from it's table and it's id
   * 
   * method will return false if the row does not exist
   */
  $db->get("people", 0);

  /**
   * translating from a path
   * 
   * this method return the translated text that correspond to the path and current locale, 
   * locale can be changed using cookie named locale or with premade method changeLocale()
   */
  translate("TEST.1");
  

});

