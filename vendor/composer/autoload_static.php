<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2cb647200cb1099e9c965554243b5d53
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Anastalal\\LaravelTranslationImporter\\' => 37,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Anastalal\\LaravelTranslationImporter\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2cb647200cb1099e9c965554243b5d53::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2cb647200cb1099e9c965554243b5d53::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit2cb647200cb1099e9c965554243b5d53::$classMap;

        }, null, ClassLoader::class);
    }
}
