<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit187ecfa425b7b3e7fabf0a834ce35801
{
    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit187ecfa425b7b3e7fabf0a834ce35801::$classMap;

        }, null, ClassLoader::class);
    }
}