<?php

if (! function_exists('array_get')) {
    /**
     * Convienience method for getting objects from an array or object.
     *
     * @param array|object
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if (is_array($array)) {

            if (array_key_exists($key, $array)) {
                return $array[$key];
            }

            foreach (explode('.', $key) as $segment) {
                if (array_key_exists($segment, $array)) {
                    $array = $array[$segment];
                } else {
                    return $default;
                }
            }

            return $array;
        }

        if (is_object($array) && property_exists($array, $key)) {
            return $array->{$key};
        }

        return $default;
    }
}

if (! function_exists('form_val')) {
    /**
     * Sanitise a value before outputting it.
     *
     * @return bool
     */
    function form_val($value)
    {
        $value = urldecode($value);
        $value = html_entity_decode($value);
        $value = trim($value);
        $value = htmlentities($value, ENT_QUOTES, 'UTF-8');

        return $value;
    }
}

/**
 * Check if the current path is to the login page.
 *
 * @return bool
 */
function is_camra_auth_login_path()
{
    $path = parse_url(array_get($_SERVER, 'REQUEST_URI'), PHP_URL_PATH);
    $path = ltrim($path, '/');
    $path = rtrim($path, '/');

    return $path === 'login';
}

/**
 * Check if the current path is to the logout page.
 *
 * @return bool
 */
function is_camra_auth_logout_path()
{
    $path = parse_url(array_get($_SERVER, 'REQUEST_URI'), PHP_URL_PATH);
    $path = ltrim($path, '/');
    $path = rtrim($path, '/');

    return $path === 'logout';
}

/**
 * Try and initialise a member from the session to determine whether they're 
 * logged in.
 *
 * @return bool
 */
function is_camra_auth_member_logged_in()
{
    return CAMRAAuth_Member::init()->authentic();
}
