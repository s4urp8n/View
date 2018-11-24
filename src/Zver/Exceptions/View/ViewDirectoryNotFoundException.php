<?php

namespace Zver\Exceptions\View {

    class ViewDirectoryNotFoundException extends \Exception
    {
        protected $message = 'View directory not exists or not a directory';
    }
}