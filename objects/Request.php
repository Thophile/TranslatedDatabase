<?php
//Block access from file
if(__FILE__ == $_SERVER['SCRIPT_FILENAME']){
    include_once $_SERVER['DOCUMENT_ROOT'].'/errors/403.html';
    die();
}

/**
 * The class that represent the request
 * 
 * @author Thophile
 * @license MIT
 */
class Request
{
  /**
   * Request object constructor
   */
  function __construct()
  {
    $this->bootstrapSelf();
  }

  
  /**
   * Set _SERVER row as properties of this object
   */
  private function bootstrapSelf()
  {
    foreach($_SERVER as $key => $value)
    {
      $this->{$this->toCamelCase($key)} = $value;
    }
  }

  /**
   * change a string to be camel case
   * @param string $string The string to be formatted
   * @return string The formatted string
   */
  private function toCamelCase($string)
  {
    $result = strtolower($string);
        
    preg_match_all('/_[a-z]/', $result, $matches);

    foreach($matches[0] as $match)
    {
        $c = str_replace('_', '', strtoupper($match));
        $result = str_replace($match, $c, $result);
    }

    return $result;
  }

  /**
   * Get the request body and sanitize it before returning
   * @return array|void The sanitized request body
   */
  public function getBody()
  {
    if($this->requestMethod === "GET")
    {
      return;
    }


    if ($this->requestMethod == "POST")
    {

      $body = array();
      foreach($_POST as $key => $value)
      {
        $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
      }

      return $body;
    }
  }
}