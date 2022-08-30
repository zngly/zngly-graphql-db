<?php

namespace ZnglyOrm\Scripts;

// create an abstract class 
abstract class CMDRunner
{
    private static $instance = null;

    public $cwd = null;

    // construct
    protected function __construct()
    {
    }

    private static function get_self()
    {
        if (self::$instance == null)
            self::$instance = new Migrate();
        return self::$instance;
    }

    public static function init()
    {
        $instance = self::get_self();
        $instance->cwd = getcwd();

        try {
            $instance->run();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    abstract public function run();

    public static function log(mixed ...$message)
    {
        $prefix = "clog: ";

        echo "\n";
        echo $prefix;

        foreach ($message as $msg) {
            // if the message is a string, then print it
            if (is_string($msg)) {
                echo $msg;
            } else {
                // if the message is an array, then print it as json
                echo json_encode($msg);
            }
        }

        echo PHP_EOL;
        echo "\n";
    }



    // delete a directory and all its contents
    public static function delete_dir(string $dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        self::delete_dir($dir . "/" . $object);
                    else
                        unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    // function to copy a folder recursively
    public static function copy_folder($src, $dest)
    {
        $dir = opendir($src);
        @mkdir($dest);
        while (false !== ($file = readdir($dir)))
            if (($file != '.') && ($file != '..'))
                if (is_dir($src . '/' . $file)) self::copy_folder($src . '/' . $file, $dest . '/' . $file);
                else copy($src . '/' . $file, $dest . '/' . $file);
        closedir($dir);
    }
}
