<?php

class Autoload
{
    public static function load($className)
    {

        $tab = explode("\\", $className);

        if($tab[0] == 'Backoffice' OR $tab[0] == 'Front'){
            $path = __DIR__.'/../src/'. implode('/', $tab) . '.php';
        } elseif($tab[0] == 'App'){
            $path = __DIR__.'/../app/'.implode('/', array_splice($tab, 1)) . '.php';
        } else{
            $tab[count($tab)-1] = ucfirst($tab[count($tab) -1]);
            $path = __DIR__ . '/'. implode('/', $tab). '.class.php';
        }

        include_once($path);
    }
}

spl_autoload_register(array("Autoload", "load"));
