<?php
/**
 * Created by PhpStorm.
 * User: varaprasad.pudi
 * Date: 2/6/18
 * Time: 10:38 AM
 */

use Conduit\Support\Optional;

if (! function_exists('optional')) {
    /**
     * Provide access to optional objects.
     *
     * @param  mixed  $value
     * @return mixed
     */
    function optional($value = null)
    {
        return new Optional($value);
    }
}