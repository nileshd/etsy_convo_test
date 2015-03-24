<?php

class EtsyException extends Exception
{

    public function __construct($message = null, $code = 101, $previous = null)
    {
        parent::__construct($message , $code, $previous);
    }

    // custom string representation of object
    public function __toString()
    {
        $error_objet = new stdClass();
        $error_object->code = $this->code;
        $error_objet->message = $this->message;

        return __CLASS__ . ":[{$this->code}]: {$this->message}\n";
    }


    public function __toJSON()
    {
        $error_object = new stdClass();
        $error_object->code = $this->code;
        $error_object->message = $this->message;

        return json_encode($error_object);
    }


}