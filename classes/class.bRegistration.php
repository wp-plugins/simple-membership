<?php

/**
 * Description of BRegistration
 *
 * @author nur
 */
abstract class BRegistration {
    protected static $_intance = null;
    protected function __construct() {
        ;
    }
    public static function get_instance(){
        $cls = static::$__CLASS__;
        self::$_intance = empty(self::$_intance)? new $cls():self::$_intance;
        return self::$_intance;
    }
}
