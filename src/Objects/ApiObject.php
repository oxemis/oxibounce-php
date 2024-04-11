<?php

namespace Oxemis\OxiBounce\Objects;

use stdClass;

abstract class ApiObject
{

    /**
     * This method is used to transform stdClass objects (returned by calling the api) into
     * ApiObjects. If a class needs a special transformation it can declare a "myMapFromStdClass"
     * method. This method will be called automatically.
     *
     * @param stdClass $s
     * @return mixed
     */
    public static function mapFromStdClass(stdClass $s)
    {

        $className = get_called_class();
        $o = new $className();

        $stdProperties = get_object_vars($s);
        foreach ($stdProperties as $property => $value) {
            if (method_exists($o, "set" . $property)) {
                $o->{"set" . $property}($value);
            }
        }

        // That method allows custom object loading
        // For example for JSON object with sub structures
        if (method_exists($o, "myMapFromStdClass")) {
            $o->{"myMapFromStdClass"}($s);
        }

        return $o;

    }

}
