<?php
//Block access from file
if(__FILE__ == $_SERVER['SCRIPT_FILENAME']){
    include_once $_SERVER['DOCUMENT_ROOT'].'/errors/403.html';
    die();
}

/**
 * The class that is responsible for database connection and that stores and handle allowed queries
 * 
 * @author Thophile
 * @license MIT
 */
class Database{
    private $data = [];
    private $file;

    function __construct(){
        $this->file = env('DATABASE');
        if(file_exists($this->file)){
            $this->data = json_decode(file_get_contents($this->file), true);
        }else{
            //No file error
            http_response_code(500);
            include($_SERVER['DOCUMENT_ROOT'].'/errors/500.html'); 
            die();

        }
    }

    function getAll(String $table){
        return isset($this->data[$table]) ? $this->data[$table] : [] ;
    }
    
    function get(String $table, String $id){

        if(isset($this->data[$table][$id])){
            return $this->data[$table][$id];
        }else return false;
    }

    function set(String $table, array $row ){
        if(!isset($this->data[$table])) $this->createTable($table);

        

        if(isset($row['ID']) && $this->get($table, $row['ID'])){
            $this->update($table, $row);
        }else{
            $this->create($table, $row);
        }
        
    }

    function update(String $table, array $row){
        
        $id = array_column($this->data[$table], 'ID');
        $rowKey = array_search($row['ID'], $id);
        
        //if translator module existe parse data to send for translation generation
        if(class_exists("Translator")){
           $row = $this->sendToTranslation($table, $row, Translator::$locale);
        }

        $this->data[$table][$rowKey] = $row;
        $this->writeData();
    }

    function create(String $table, array $row){
        
        if(empty($this->data[$table])){
            $row['ID'] = 0;
        } else{
            $row['ID'] = array_key_last($this->data[$table]) + 1;
        }
        
        //if translator module existe parse data to send for translation generation
        if(class_exists("Translator")){
            $row = $this->sendToTranslation($table, $row);
        }
        $this->data[$table][] = $row;

        
        $this->writeData();
        return $row['ID'];
    }

    function delete(String $table, $id){

        unset($this->data[$table][$id]);

        $this->writeData();
    }

    function createTable(String $table){
        $this->data[$table] = [];
        $this->writeData();
    }

    function writeData(){
        $f = fopen($this->file, 'w');
        fwrite($f, json_encode($this->data, JSON_FORCE_OBJECT));
        fclose($f);
    }

    function sendToTranslation($table, $row, $locale = null){
        //create array to associate textcode with value
        $translationList = [];
        $slug = array($table,$row['ID']);
        //declare recursion and then call it
        function findTranslation(array $haystack, array $path = [], &$translationList, $slug) {
            foreach ($haystack as $key => $value) {
                $currentPath = array_merge($path, [$key]);
                if (is_array($value)) {
                    findTranslation($value, $currentPath, $translationList, $slug);
                } else {
                    //capitals separated by dots
                    $textCode = strtoupper(implode(".",array_merge($slug, $currentPath)));
                    var_dump($textCode);
                    $translationList[$textCode] = $value;
                }
            }
        }
        findTranslation($row,[], $translationList, $slug);


        var_dump($translationList);
        foreach ($translationList as $textCode => $value) {
            //if translation does not exist set it on all else set it on locale
            if(!translate($textCode)){
                setTranslation($textCode,$value);
            }else{
                //globally accessible function declared in Translator
                setTranslation($textCode,$value, $locale);
            }

            //replace row value with text code after translation has  been set

            //get relevant code
            $tree = explode(".", $textCode, 3);
            $tree= explode(".",$tree[2]);

            $tmp =& $row;
            $flag = false;
                foreach($tree as $key) {
                    $key == 'ID' ? $flag = true : $flag = false;
                    $tmp =& $tmp[$key];
                }
                if ($flag) continue;
                $tmp = $textCode;

        } 
        return $row;
    }
}