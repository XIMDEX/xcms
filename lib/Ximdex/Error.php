<?php
    namespace Ximdex;

    class Error
    {
        /**
         * return the related message from the last PHP error, or null if there is not
         * @return string|null
         */
        public static function error_message()
        {
            $error = error_get_last();
            if ($error)
                return $error['message'];
            return null;
        }
    }