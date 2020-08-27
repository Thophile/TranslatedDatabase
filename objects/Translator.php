<?php
//Block access from file
if(__FILE__ == $_SERVER['SCRIPT_FILENAME']){
    include_once $_SERVER['DOCUMENT_ROOT'].'/errors/403.html';
    die();
}

/**
 * The class that is responsible for authentication
 * 
 * @author Thophile
 * @license MIT
 */
class Translator
{
    public static $instance = null;
    public static $dictionary = [];
    public static $locale;

    function __construct(){

        //get default locale
        //if set on cookie take from cookie else :
        if(isset($_COOKIE["locale"])){
            self::$locale = $_COOKIE["locale"];
        }else{
            self::$locale = env("DEFAULT_LOCALE");
        }

        //get dictionary data
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/translations/'.self::$locale.".json")){
            self::$dictionary = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/translations/'.self::$locale.".json"), true);
        }else{
            //No file error
            http_response_code(500);
            include($_SERVER['DOCUMENT_ROOT'].'/errors/500.html');
            die();
        }


        /**
         * @param String $textCode the text code that point on the text we need
         */
        function translate(String $textCode){

            //recursively search text in nested array corresponding to textcode tree
            $tree = explode(".", $textCode);
            $text = Translator::$dictionary;
            foreach ($tree as $branche) {
                if(!isset($text[$branche])) return false;
                $text = $text[$branche];
            }
            return $text;
        }
        function changeLocale(String $locale){
        
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/translations/'.$locale.".json")){
                setcookie("locale", $locale);
                Translator::$locale = $locale;
                Translator::$dictionary = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/translations/'.Translator::$locale.".json"), true);
            }else{
                //No file error
                http_response_code(500);
                include($_SERVER['DOCUMENT_ROOT'].'/errors/500.html'); 
                die();
            }
        }

        function setTranslation($textCode, $value, $locale = null){

            //set in all file by default or in specified locale if set
            if($locale == null){
                $translations = scandir($_SERVER['DOCUMENT_ROOT'].'/translations/'.$filename);
            }else{
                $translations[] = $locale.".json";
            }

            
            foreach ($translations as $key => $filename) {
                if(is_dir($_SERVER['DOCUMENT_ROOT'].'/translations/'.$filename)){
                    continue;
                }
                $data = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/translations/'.$filename),true);
                $tree = explode(".", $textCode);

                $tmp =& $data;
                foreach($tree as $key) {
                    $tmp =& $tmp[$key];
                }
                $tmp = $value;

                //write file
                $f = fopen($_SERVER['DOCUMENT_ROOT'].'/translations/'.$filename, 'w');
                fwrite($f, json_encode($data, JSON_FORCE_OBJECT));
                fclose($f);
            }
        }
    }
    /*
    static function getTranslator(){
        if(is_null(self::$instance)){
            self::$instance = new Translator();
        }
        return self::$instance;
        
    }*/

    

    

}