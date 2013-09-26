<?php


class BlogSimpleAutoloader
{

    public static function register($prepend = false)
    {
        if (version_compare(phpversion(), '5.3.0', '>=')) {
            spl_autoload_register(array(new self, 'autoload'), true, $prepend);
        } else {
            spl_autoload_register(array(new self, 'autoload'));
        }
    }


    public static function autoload($class)
    {
        if (0 !== strpos($class, 'BlogSimple')) {
            return;
        }
        $file = dirname(__DIR__) . '/' .str_replace( '\\', '/', $class ) . '.php';
        if ( is_file( $file ) ) {
            require $file;
        }
    }
}
