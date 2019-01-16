<?php

namespace Zver\Exceptions\View {

    /**
     * Class ViewDirectoryNotFound
     * @package Zver\Exceptions\View
     */
    class ViewDirectoryNotFound extends \Exception
    {
        /**
         * ViewDirectoryNotFound constructor.
         * @param $directory
         */
        public function __construct($directory)
        {
            parent::__construct('View directory  "' . $directory . '" not exists or not a directory');
        }
    }
}