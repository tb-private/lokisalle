<?php

namespace app;

use Yaml\Spyc;

class Config
{
    protected $parameters;
    private static $instance = null;

    private function __construct()
    {
        require __DIR__.'/config/parameters.php';
        $this->parameters = $parameters;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Config();  //parenthèses facultatives car pas d'argument
        }

        return self::$instance;
    }

    public function getParametersConnect()
    {
        return $this->parameters['connect'];
    }

    public function getParametersMenu($role)
    {
        return $this->parameters['menu'][$role];
    }

    public function getRoutesConfig()
    {
        $routes = Spyc::YAMLLoad(__DIR__.'/config/routes.yml');

        return $routes;
    }
}
