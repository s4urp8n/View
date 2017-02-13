<?php
namespace Zver\Exceptions\View {

    class ViewNotFoundException extends \Exception
    {

        /**
         * Override parent constructor to see custom exception message
         *
         * @param string $message
         */
        public function __construct($filePath)
        {
            parent::__construct(' View file "' . $filePath . '" not found');
        }

    }
}