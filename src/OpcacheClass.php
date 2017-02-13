<?php

namespace Appstract\Opcache;

use File;

/**
 * Class OpcacheClass.
 */
class OpcacheClass
{
    /**
     * OpcacheClass constructor.
     */
    public function __construct()
    {
        // constructor body
    }

    /**
     * Clear the cache.
     *
     * @return bool
     */
    public function clear()
    {
        if (function_exists('opcache_reset')) {
            return opcache_reset();
        }

        return false;
    }

    /**
     * Get configuration values.
     *
     * @return mixed
     */
    public function getConfig()
    {
        if (function_exists('opcache_get_configuration')) {
            $config = opcache_get_configuration();

            return $config ?: false;
        }

        return false;
    }

    /**
     * Get status info.
     *
     * @return mixed
     */
    public function getStatus()
    {
        if (function_exists('opcache_get_status')) {
            $status = opcache_get_status(false);

            return $status ?: false;
        }

        return false;
    }

    /**
     * Precompile app (WIP).
     *
     * @return bool | array
     */
    public function optimize()
    {
        if (! function_exists('opcache_compile_file')) {
            return false;
        }

        // Get files in these paths
        $files = File::allFiles([
            base_path('app'),
            base_path('bootstrap'),
            base_path('storage/framework/views'),
            base_path('routes'),
            base_path('vendor/laravel/framework'),
        ]);

        $files = collect($files);

        // filter on php extension
        $files = $files->filter(function ($value) {
            return File::extension($value) == 'php';
        });

        // optimize files
        $optimized = 0;

        $files->each(function ($file) use (&$optimized) {
            if (@opcache_compile_file($file)) {
                $optimized++;
            }
        });

        return [
            'total_files_count' => $files->count(),
            'compiled_count'    => $optimized,
        ];
    }
}
