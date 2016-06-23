<?php

$config = include 'tests' . DIRECTORY_SEPARATOR . 'config.php';

//Build commands array
$commands = [
    [
        'description' => 'Package documentation generation started...',
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
        'description' => 'Cleaning documentation...',
        'callback'    => function () use ($config)
        {
            $removes = [
                'docs',
            ];

            foreach ($removes as $remove)
            {
                $config['removeFunction']($remove);
            }
        },
    ],
    [
        'description' => 'Generating documentation using phpDocumentor...',
        'command'     => 'php vendor' . DIRECTORY_SEPARATOR . 'phpdocumentor' . DIRECTORY_SEPARATOR . 'phpdocumentor'
                         . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'phpdoc -d .' . DIRECTORY_SEPARATOR
                         . 'src -t .' . DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR . 'html --template="vendor'
                         . DIRECTORY_SEPARATOR . 'cvuorinen' . DIRECTORY_SEPARATOR . 'phpdoc-markdown-public'
                         . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR
                         . 'markdown-public" --template="responsive-twig" --title="' . $config['packageName']
                         . '" --visibility="public" --sourcecode',
    ],
    [
        'description' => 'Copy markdown template...',
        'callback'    => function () use ($config)
        {
            @mkdir('docs' . DIRECTORY_SEPARATOR . 'markdown', 0777, true);
            @copy(
                implode(DIRECTORY_SEPARATOR, ['docs', 'html', 'README.md']),
                implode(DIRECTORY_SEPARATOR, ['docs', 'markdown', 'API.md'])
            );
            @unlink(implode(DIRECTORY_SEPARATOR, ['docs', 'html', 'README.md']));
            $config['removeFunction'](
                'docs' . DIRECTORY_SEPARATOR . 'html', function ($path)
            {
                if (strpos(basename($path), 'phpdoc-cache') !== false)
                {
                    return true;
                }

                return false;
            }
            );
        },
    ],
    [
        'description' => 'Add documentation to Git...',
        'command'     => 'git add docs/*',
    ],
];

//Executing commands and show output
call_user_func_array($config['commandExecutor'], [$commands]);
