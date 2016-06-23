<?php
/**
 * To check some variables/methods/classes which use session
 */
session_start();

/**
 * Autoload from SRC using PSR-4
 */
spl_autoload_register(
    function ($className)
    {
        $srcDir = __DIR__ . DIRECTORY_SEPARATOR . implode(
                DIRECTORY_SEPARATOR, [
                                       '..',
                                       'src',
                                   ]
            );
        
        $fileName = realpath($srcDir) . DIRECTORY_SEPARATOR . str_replace(
                '/', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\//'))
            ) . '.php';
        
        if (file_exists($fileName))
        {
            include_once $fileName;
        }
        
    }
);

/**
 * Autoload from tests/files/classes using PSR-4
 */
spl_autoload_register(
    function ($className)
    {
        $srcDir = __DIR__ . DIRECTORY_SEPARATOR . implode(
                DIRECTORY_SEPARATOR, [
                                       'files',
                                       'classes',
                                   ]
            );
        $fileName = realpath($srcDir) . DIRECTORY_SEPARATOR . str_replace(
                '/', DIRECTORY_SEPARATOR, str_replace('\\', DIRECTORY_SEPARATOR, trim($className, '\//'))
            ) . '.php';
        if (file_exists($fileName))
        {
            include_once $fileName;
        }
    }
);

$composer = realpath(
    __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'
);

include($composer);