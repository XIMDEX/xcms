<?php
    namespace Ximdex;

    class Error
    {
        /**
         * return the related message from the last PHP error, or null if there is not
         * @return string|null
         */
        public static function error_message($replace = null)
        {
            $error = error_get_last();
            if ($error)
            {
                $error = $error['message'];
                if ($replace)
                    $error = str_ireplace($replace, '', $error);
                return $error;
            }
            return null;
        }
    }