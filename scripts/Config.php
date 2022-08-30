<?php

namespace ZnglyOrm\Scripts;

class Config
{
    public $path_app;
    public $path_app_config;
    public $path_composer;

    public $apps;

    public function __construct(
        public $cwd = null,
    ) {
        if ($this->cwd == null) {
            $this->cwd = getcwd();
        }


        $this->path_app = self::format_path($this->cwd . '/app');
        $this->path_app_config = self::format_path($this->path_app . '/app.json');
        $this->path_composer = self::format_path($this->cwd . '/composer.json');

        $this->apps = $this->get_apps();
    }

    public function get_app_json()
    {
        return json_decode(file_get_contents($this->path_app_config));
    }

    public static function format_path(string $path)
    {
        return str_replace('\\', '/', $path);
    }

    private function get_apps(): array
    {
        // get all the folders in the app folder
        $folders = scandir($this->path_app);

        // apps must begin with capital letter and only contain A-Z, a-z
        $apps = [];
        foreach ($folders as $folder) {
            if (preg_match('/^[a-z]{1}[a-z_]{1,}$/', $folder)) {
                $apps[] = $folder;
            }
        }

        CMDRunner::log("found apps -> ", $apps);

        return $apps;
    }
}
