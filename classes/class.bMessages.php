<?php
/**
 *
 *
 * @author nur
 */
class BMessages {
    private $messages;
    public function __construct() {
        $this->messages = array();
    }
    public function get($key){
        if(isset($this->messages[$key])){
            $m = $this->messages[$key];
            $this->messages[$key] ='';
            return $m;
        }
        return '';
    }
    public function set($key, $value){
        $this->messages[$key] = $value;
    }
}
