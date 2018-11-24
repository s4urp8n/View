<?php

namespace Zver\Exceptions\View {

    class ViewDirectoryNotFoundException extends \Exception
    {

        /**
         * Override parent constructor to see custom exception message
         *
         * @param string $message
         */
        public function __construct($directory)
        {
            parent::__construct('View directory  "' . $directory . '" not exists or not a directory');
        }
    }
}