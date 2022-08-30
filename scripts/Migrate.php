<?php

namespace ZnglyOrm\Scripts;

class Migrate extends CMDRunner
{
    const MODEL_NAME = '_model.php';

    public Config $config;

    public function run()
    {
        $this->config = new Config($this->cwd);
        $this->get_models();
    }


    // function to look for all the classes that extend ZnglyOrm\Model\Model
    private function get_models()
    {

        // get composer file
        self::log($this->config->apps);

        // $classes = get_declared_classes();
        // self::log(json_encode($classes));

        foreach ($this->config->apps as $_ => $value) {
            $app_path = $this->config->path_app . '/' . $value;
            $this->app_compute($app_path);
        }
    }

    private function app_compute(string $path)
    {

        // do model search
        $this->model_compute($path);
    }

    private function model_compute(string $path)
    {
        $files = scandir($path);
        $models = [];
        foreach ($files as $file) {
            // a valid model file ends with _model.php
            if (preg_match('/^[a-z]{1}[a-z_]{1,}' . self::MODEL_NAME . '$/', $file)) {
                $model_path = $this->config->format_path($path . '/' . $file);
                $models[] = $model_path;
            }
        }

        self::log($models);
    }
}
