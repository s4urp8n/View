<?php

$webServerHandle = null;
$codeceptionStatus = null;
$config = include 'tests' . DIRECTORY_SEPARATOR . 'config.php';

//Build commands array
$commands = [
    [
        'description' => 'Package testing started...',
    ],
    [
        'callback'    => function ()
        {
            //Turn on implicit flush
            ob_implicit_flush(true);

            //Change shell directory to current
            shell_exec(escapeshellcmd('cd ' . __DIR__));
        },
        'description' => 'Changing directory to ' . __DIR__ . ' and turning on implicit flush...',
    ],
    [
        'description' => 'Running build-in WEB-server...',
        'callback'    => function () use ($config, &$webServerHandle)
        {
            $pipes = [];
            $descriptorspec = array(
                0 => array("pipe", "r"),
                1 => array("pipe", "w"),
                2 => array("pipe", "w"),
            );

            $webServerHandle = proc_open('php -S ' . $config['server'], $descriptorspec, $pipes);
        },
    ],
    [
        'description' => 'Testing...',
        'callback'    => function () use (&$codeceptionStatus)
        {
            $command = "php vendor" . DIRECTORY_SEPARATOR . "codeception" . DIRECTORY_SEPARATOR . "codeception"
                       . DIRECTORY_SEPARATOR
                       . "codecept run --coverage --coverage-xml --coverage-html --coverage-text --fail-fast";
            passthru($command, $codeceptionStatus);
        },
    ],
    [
        'description' => 'Terminating build-in WEB-server...',
        'callback'    => function () use (&$webServerHandle)
        {
            proc_terminate($webServerHandle);
        },
    ],
    [
        'description' => 'Add changes to Git...',
        'command'     => 'git add tests/*',
    ],
];

//Executing commands and show output
call_user_func_array($config['commandExecutor'], [$commands]);

exit($codeceptionStatus);