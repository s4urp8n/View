<?php
clearstatcache(true);

$srcDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'src') . DIRECTORY_SEPARATOR;
$composerDirectory = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor') . DIRECTORY_SEPARATOR;

//Composer autoload
$composerFile = $composerDirectory . 'autoload.php';
if (file_exists($composerFile)) {
    include_once($composerFile);
}

/**
 * Register autoloading PSR-4 from directory
 *
 * @param string $directory
 */
function registerAutoloadClassesFrom($directory)
{
    spl_autoload_register(function ($className) use ($directory) {
        $realDirectory = realpath($directory);
        if (file_exists($realDirectory)) {
            /**
             * Trailing slash
             */
            $realDirectory = mb_eregi_replace(preg_quote(DIRECTORY_SEPARATOR) . '+$', '', $realDirectory);
            /**
             * Full class name
             */
            $fileName = $realDirectory . DIRECTORY_SEPARATOR
                        . trim($className, '\\/')
                        . '.php';
            if (file_exists($fileName)) {
                include_once $fileName;
            }
        }
    });
}

registerAutoloadClassesFrom($srcDirectory);

//Functions file autoload
$functionsFile = $srcDirectory . 'Functions.php';
if (file_exists($functionsFile)) {
    include_once($functionsFile);
}
