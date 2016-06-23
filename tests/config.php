<?php

return [
    'server'           => "127.0.0.1:4444",
    'packageName'      => "Zver\\View",
    'downloadFunction' => function ($link, $file)
    {
        if (!file_exists($file))
        {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_REFERER, $link);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $content = curl_exec($curl);
            curl_close($curl);
            
            file_put_contents($file, $content, LOCK_EX);
        }
    },
    'commandExecutor'  => function ($commands)
    {
        $comandsCount = count($commands);
        
        for ($i = 0; $i < $comandsCount; $i++)
        {
            if ($i == 0)
            {
                echo "\n\n";
            }
            if (!empty($commands[$i]['description']))
            {
                echo $commands[$i]['description'] . "\n\n";
            }
            
            if (!empty($commands[$i]['command']))
            {
                echo passthru($commands[$i]['command']) . "\n\n";
            }
            
            if (!empty($commands[$i]['callback']))
            {
                call_user_func(($commands[$i]['callback']));
            }
            if ($i == $comandsCount - 1)
            {
                echo "\n\n";
            }
        }
    },
    'removeFunction'   => function ($path, $callback = null)
    {
        if (file_exists($path))
        {

            if (is_file($path))
            {
                if (is_null($callback) || (is_callable($callback) && $callback($path) === true))
                {
                    @unlink($path);
                }
            }
            else
            {

                $iterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator(
                    $iterator, RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($files as $file)
                {
                    if ($file->isDir())
                    {
                        if (is_null($callback) || (is_callable($callback) && $callback($file->getRealPath()) === true))
                        {
                            @rmdir($file->getRealPath());
                        }
                    }
                    else
                    {
                        if (is_null($callback) || (is_callable($callback) && $callback($file->getRealPath()) === true))
                        {
                            @unlink($file->getRealPath());
                        }
                    }
                }
                if (is_null($callback) || (is_callable($callback) && $callback($path) === true))
                {
                    @rmdir($path);
                }
            }

        }
    },
];