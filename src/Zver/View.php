<?php

namespace Zver {

    use Zver\Exceptions\View\ViewDirectoryNotFound;
    use Zver\Exceptions\View\ViewNotFound;

    /**
     * Template engine with auto escaping html entities and with full PHP-native code support
     *
     * @package Zver
     */
    class View
    {

        /**
         * @var string
         */
        protected static $encoding = 'UTF-8';
        /**
         * @var string
         */
        protected static $ext = '.php';
        /**
         * @var string
         */
        protected static $dataKeySeparator = '|';
        /**
         * @var array
         */
        protected static $searchDirectories = [];

        /**
         * @var
         */
        protected $content;
        /**
         * @var array
         */
        protected $data = [];

        protected function __construct()
        {
        }

        /**
         * @param $string
         * @param $end
         * @return string
         */
        protected static function ensureEnds($string, $end)
        {
            $currentEnd = mb_substr($string, -mb_strlen($end, static::$encoding), null, static::$encoding);

            if ($currentEnd == $end) {
                return $string;
            }

            return $string . $end;
        }

        /**
         * @param $path
         * @return false|string
         */
        protected static function replaceSlashes($path)
        {
            return mb_eregi_replace('[' . preg_quote('/') . preg_quote('\\') . ']+', DIRECTORY_SEPARATOR, $path);
        }

        /**
         * @param $dir
         * @throws \Zver\Exceptions\View\ViewDirectoryNotFound
         */
        public static function addViewsDirectory($dir)
        {
            if (!empty($dir)) {

                clearstatcache(true);

                $dir = static::replaceSlashes(static::ensureEnds($dir, DIRECTORY_SEPARATOR));

                if (is_dir($dir)) {
                    if (!in_array($dir, static::$searchDirectories)) {
                        static::$searchDirectories = array_merge([$dir], static::$searchDirectories);
                    }
                } else {
                    throw new ViewDirectoryNotFound($dir);
                }
            }
        }

        /**
         * @return array
         */
        public static function getViewsDirectories()
        {
            return static::$searchDirectories;
        }

        /**
         * @param $path
         * @return bool|string
         */
        protected static function findView($path)
        {
            if (!empty($path)) {

                clearstatcache(true);

                $absolutePath = static::ensureEnds(static::replaceSlashes($path), static::$ext);

                /**
                 * Check if path is absolute
                 */
                if (file_exists($absolutePath)) {
                    return $absolutePath;
                }

                /**
                 * Ensure path format
                 */
                $viewName = mb_ereg_replace('^[' . preg_quote('/') . preg_quote('\\') . '\s]+', '', $path);
                $viewName = static::replaceSlashes(static::ensureEnds($viewName, static::$ext));

                /**
                 * Search in directories
                 */
                $fullName = '';

                foreach (static::$searchDirectories as $directory) {
                    $fullName = $directory . $viewName;
                    if (file_exists($fullName)) {
                        return $fullName;
                    }
                }

            }

            return false;
        }

        /**
         * @param       $fileOrString
         * @param array $data
         * @return \Zver\View
         * @throws \Zver\Exceptions\View\ViewNotFound
         */
        public static function load($fileOrString, array $data = [])
        {
            $view = static::findView($fileOrString);

            if ($view == false) {
                return static::loadFromString($fileOrString, $data);
            }
            return static::loadFromFile($view, $data);
        }

        /**
         * Create view instance, set content and data from second argument
         *
         * @param string $file
         * @param array  $data
         *
         * @return static
         * @throws \Zver\Exceptions\View\ViewNotFound
         */
        public static function loadFromFile($file, array $data = [])
        {
            if (file_exists($file)) {
                $object = new static();
                $object->setContent(file_get_contents($file));
                foreach ($data as $key => $value) {
                    $object->set($key, $value);
                }

                return $object;
            }

            throw new ViewNotFound($file);
        }

        /**
         * Set content to processing with eval()
         *
         * @param $content
         */
        protected function setContent($content)
        {
            $this->content = '?>' . $content;
        }

        /**
         * Store value associated with key in data array
         *
         * @param string $key
         * @param mixed  $value
         *
         * @return $this
         */
        public function set($key, $value)
        {
            $trimmedKey = mb_eregi_replace('\s+', '', $key);
            if (mb_strpos($trimmedKey, static::$dataKeySeparator) === false) {
                $this->data[$trimmedKey] = $value;
            } else {
                $keys = explode(static::$dataKeySeparator, $trimmedKey);
                foreach ($keys as $key) {
                    if ($key != '') {
                        $this->data[$key] = $value;
                    }
                }
            }

            return $this;
        }

        /**
         * Create instance, set view content from string and data from second argument
         *
         * @param       $string
         * @param array $data
         *
         * @return static
         */
        public static function loadFromString($string, array $data = [])
        {
            $object = new static();
            $object->setContent($string);
            foreach ($data as $key => $value) {
                $object->set($key, $value);
            }

            return $object;
        }

        /**
         * Auto render view to string if needed
         *
         * @return string
         */
        public function __toString()
        {
            return $this->render();
        }

        /**
         * Extract data into variable and get string representation of view file
         *
         * @return string
         */
        public function render()
        {
            extract($this->data);
            ob_start();
            eval($this->processEscapedContent());

            return ob_get_clean();
        }

        /**
         * Process short syntax before output render result.
         * Variables in this method have strange names, because of overriding method variables by extracted variables
         *
         * @return string
         */
        protected function processEscapedContent()
        {
            $expressionPattern = "/\\{\\{(.+)\\}\\}/mU";
            $replacement = '<?= htmlentities($1, ENT_QUOTES, "' . static::$encoding . '", true)?>';
            $this->content = preg_replace($expressionPattern, $replacement, $this->content);

            return $this->content;
        }

        /**
         * Magic method equals to get() method
         *
         * @param $key
         *
         * @return mixed
         */
        public function __get($key)
        {
            return $this->get($key);
        }

        /**
         * Magic method equals to set() method
         *
         * @param $key
         * @param $value
         *
         * @return $this
         */
        public function __set($key, $value)
        {
            return $this->set($key, $value);
        }

        /**
         * Get value from data array. If value is not set throws ViewDataNotFoundException.
         *
         * @param string $key
         *
         * @return mixed
         */
        public function get($key)
        {
            if (array_key_exists($key, $this->data)) {
                return $this->data[$key];
            }

            return null;
        }

        /**
         * Clear all data associated with current view
         *
         * @return $this
         */
        public function resetData()
        {
            $this->data = [];

            return $this;
        }

        /**
         * Get all data associated with current view
         *
         * @return array
         */
        public function getAllData()
        {
            return $this->data;
        }

    }

}
