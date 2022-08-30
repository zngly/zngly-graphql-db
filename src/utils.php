<?php

namespace Zngly\Graphql\Db;


class Utils
{

    /**
     * Gets the model classname in a suitable namespace
     * @param string $class name
     */
    public static function runtime_class_name(string $name = "")
    {
        if ($name == "")
            return "ZnglyGraphqlDbRuntime";
        return "ZnglyGraphqlDbRuntime" . ucfirst($name);
    }

    /**
     *
     * @param string $str      Unknown.
     * @param array  $no_strip Unknown.
     *
     * @return mixed|null|string|string[]
     */
    public static function camel_case($str, array $no_strip = [])
    {
        // non-alpha and non-numeric characters become spaces.
        $str = preg_replace('/[^a-z0-9' . implode('', $no_strip) . ']+/i', ' ', $str);
        $str = trim($str);
        // Lowercase the string
        $str = strtolower($str);
        // uppercase the first character of each word.
        $str = ucwords($str);
        // Replace spaces
        $str = str_replace(' ', '', $str);
        // Lowecase first letter
        $str = lcfirst($str);

        return $str;
    }
}
