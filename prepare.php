<?php

$config = include 'tests' . DIRECTORY_SEPARATOR . 'config.php';

//Build commands array
$commands = [
    [
        'description' => 'Prepare package for testing started...',
    ],
    [
        'description' => 'Cleaning...',
        'callback'    => function () use ($config)
        {
            $removes = [
                'vendor',
                'composer.lock',
                'docs',
                'API.md',
                'tests' . DIRECTORY_SEPARATOR . '_output' . DIRECTORY_SEPARATOR . 'c3tmp',
                'c3.php',
            ];

            foreach ($removes as $remove)
            {
                $config['removeFunction']($remove);
            }
        },
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
        'description' => 'Run Composer self-update...',
        'command'     => 'composer self-update',
    ],
    [
        'description' => 'Run Composer install...',
        'command'     => 'composer install',
    ],
    [
        'description' => 'Build testing...',
        'command'     => 'php vendor' . DIRECTORY_SEPARATOR . 'codeception' . DIRECTORY_SEPARATOR . 'codeception'
                         . DIRECTORY_SEPARATOR . 'codecept build',
    ],
    [
        'description' => 'Clean testing...',
        'command'     => 'php vendor' . DIRECTORY_SEPARATOR . 'codeception' . DIRECTORY_SEPARATOR . 'codeception'
                         . DIRECTORY_SEPARATOR . 'codecept clean',
    ],
];

//Executing commands and show output
call_user_func_array($config['commandExecutor'], [$commands]);
