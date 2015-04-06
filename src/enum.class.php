<?php
abstract class enum
{
    final public function __construct($value)
    {
        $c = new ReflectionClass($this);
        if(!in_array($value, $c->getConstants())) {
            throw new ExceptionDeveloper('Illegal arguments');
        }
        $this->value = $value;
    }
 
    final public function __toString()
    {
        return $this->value;
    }
}
