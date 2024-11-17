<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0ccc93a0bff6688d753d290701303d4d
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'App\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0ccc93a0bff6688d753d290701303d4d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0ccc93a0bff6688d753d290701303d4d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit0ccc93a0bff6688d753d290701303d4d::$classMap;

        }, null, ClassLoader::class);
    }
}