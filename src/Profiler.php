<?php

namespace Profiler;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Class Profiler
 *
 * @package Profiler
 */
class Profiler {

    /**
     * @var Filesystem
     */
    protected static $fileSystem;

    /**
     * @var string
     */
    protected static $publicPath;

    /**
     * @var string
     */
    protected static $publicUrl;

    /**
     * @return Filesystem
     */
    protected static function getFileSystem(): Filesystem {

        if ( ! isset( static::$fileSystem ) ) {

            static::$fileSystem = new Filesystem();

        }

        return static::$fileSystem;

    }

    /**
     * @return string
     */
    public static function getRoot(): string {

        return __DIR__ . '/..';

    }

    /**
     * @param string $path
     * @param string $url
     */
    public static function registerPublicPath( string $path, string $url ) {

        static::$publicPath = $path;
        static::$publicUrl = $url;

    }

    public static function start() {

        if ( ! function_exists( 'tideways_xhprof_enable' ) || ! function_exists( 'tideways_xhprof_disable' ) ) {

            throw new \RuntimeException( 'Please install and enable the Tideways XHProf extension.' );

        }

        if ( ! isset( static::$publicPath ) || ! isset( static::$publicUrl ) ) {

            throw new \RuntimeException( 'Please call registerPublicPath() to configure a path (and a corresponding' .
                ' publicly accessible URL) to an empty directory where the XHProf GUI should be copied over to.' );

        }

        static::getFileSystem()->mirror( static::getRoot() . '/html', static::$publicPath );

        tideways_xhprof_enable(

            TIDEWAYS_XHPROF_FLAGS_CPU |
            TIDEWAYS_XHPROF_FLAGS_MEMORY |
            TIDEWAYS_XHPROF_FLAGS_NO_BUILTINS

        );

    }

    public static function stop() {

        $profile = tideways_xhprof_disable();

        require_once static::getRoot() . '/lib/utils/xhprof_lib.php';
        require_once static::getRoot() . '/lib/utils/xhprof_runs.php';

        $runs = new \XHProfRuns_Default();
        $id = $runs->save_run( $profile, 'profile' );

        echo '<script>window.open("' . rtrim( static::$publicUrl, '/' ) .
            '/index.php?source=profile&sort=excl_wt&run=' . $id . '")</script>';

    }

}
