<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd925709b3732ca4bb3127b5f2ef6e4dc
{
    public static $prefixLengthsPsr4 = array (
        'F' => 
        array (
            'Firebase\\JWT\\' => 13,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Firebase\\JWT\\' => 
        array (
            0 => __DIR__ . '/..' . '/firebase/php-jwt/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd925709b3732ca4bb3127b5f2ef6e4dc::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd925709b3732ca4bb3127b5f2ef6e4dc::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitd925709b3732ca4bb3127b5f2ef6e4dc::$classMap;

        }, null, ClassLoader::class);
    }
}