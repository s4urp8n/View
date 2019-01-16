<?php

namespace Zver\Exceptions\View {

    /**
     * Class ViewNotFound
     * @package Zver\Exceptions\View
     */
    class ViewNotFound extends \Exception
    {
        /**
         * ViewNotFound constructor.
         * @param $filePath
         */
        public function __construct($filePath)
        {
            parent::__construct(' View file "' . $filePath . '" not found');
        }

    }
}