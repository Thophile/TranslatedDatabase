<?php
//Prevent file access
if(__FILE__ == $_SERVER['SCRIPT_FILENAME']){
    include_once $_SERVER['DOCUMENT_ROOT'].'/errors/403.html';
    die();
}

$variables = [
    'DEFAULT_LOCALE' => 'en',
    'DATABASE' => 'DATABASE.json',
];

//Set the env variable
foreach ($variables as $key => $value) {
    putenv("$key=$value");
}

//Function to retrieve them with null when empty
function env($key, $default = null)
    {
        $value = getenv($key);
        return ($value === false ? $default : $value);
    }
?>