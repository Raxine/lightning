<?php

/**
 * PARAM_RAW specifies a parameter that is not cleaned/processed in any way except the discarding of the invalid utf-8 characters
 */
define('PARAM_RAW', 'raw');

/**
 * PARAM_INT - integers only, use when expecting only numbers.
 */
define('PARAM_INT', 'int');

/**
 * PARAM_FLOAT - a real/floating point number.
 *
 * Note that you should not use PARAM_FLOAT for numbers typed in by the user.
 * It does not work for languages that use , as a decimal separator.
 * Instead, do something like
 *     $rawvalue = required_param('name', PARAM_RAW);
 *     // ... other code including require_login, which sets current lang ...
 *     $realvalue = unformat_float($rawvalue);
 *     // ... then use $realvalue
 */
define('PARAM_FLOAT', 'float');

/**
 * PARAM_ALPHA - contains only english ascii letters a-zA-Z.
 */
define('PARAM_ALPHA', 'alpha');

/**
 * PARAM_ALPHAEXT the same contents as PARAM_ALPHA plus the chars in quotes: "_-" allowed
 * NOTE: originally this allowed "/" too, please use PARAM_SAFEPATH if "/" needed
 */
define('PARAM_ALPHAEXT', 'alphaext');

/**
 * PARAM_ALPHANUM - expected numbers and letters only.
 */
define('PARAM_ALPHANUM', 'alphanum');

/**
 * PARAM_ALPHANUMEXT - expected numbers, letters only and _-.
 */
define('PARAM_ALPHANUMEXT', 'alphanumext');

/**
 * PARAM_BOOL - converts input into 0 or 1, use for switches in forms and urls.
 */
define('PARAM_BOOL', 'bool');

/**
 * 
 * @param string $paramName
 * @param string $paramType
 * 
 * PARAM_RAW
 * PARAM_INT
 * PARAM_FLOAT
 * PARAM_ALPHA
 * PARAM_ALPHAEXT
 * PARAM_ALPHANUM
 * PARAM_ALPHANUMEXT
 * PARAM_BOOL
 * 
 * @param type $defaultValue
 * @return type
 */
function optionnal_param($paramName, $paramType, $defaultValue) {
    if (isset($_POST[$paramName])) {
        $param = $_POST[$paramName];
    } else if (isset($_GET[$paramName])) {
        $param = $_GET[$paramName];
    } else {
        return $defaultValue;
    }

    return clean_param($param, $paramType);
}

/**
 * 
 * @param string $paramName
 * @param string $paramType: 
 * 
 * PARAM_RAW
 * PARAM_INT
 * PARAM_FLOAT
 * PARAM_ALPHA
 * PARAM_ALPHAEXT
 * PARAM_ALPHANUM
 * PARAM_ALPHANUMEXT
 * PARAM_BOOL
 * 
 */
function required_param($paramName, $paramType) {
    if (isset($_POST[$paramName])) {
        $param = $_POST[$paramName];
    } else if (isset($_GET[$paramName])) {
        $param = $_GET[$paramName];
    } else {
        //print_error('missingparam', '', '', $paramName);
        echo 'missing param: ' . $paramName;
    }
    
    return cleanParam($param, $paramType);
}

/**
 * 
 * @param string $param
 * @param string $paramType
 * @return type
 * @throws coding_exception
 * 
 * PARAM_RAW
 * PARAM_INT
 * PARAM_FLOAT
 * PARAM_ALPHA
 * PARAM_ALPHAEXT
 * PARAM_ALPHANUM
 * PARAM_ALPHANUMEXT
 * PARAM_BOOL
 * 
 */
function cleanParam($param, $paramType) {
    if (is_array($param)) {
        throw new coding_exception('clean_param() can not process arrays, please use clean_param_array() instead.');
    } else if (is_object($param)) {
        if (method_exists($param, '__toString')) {
            $param = $param->__toString();
        } else {
            throw new coding_exception('clean_param() can not process objects, please use clean_param_array() instead.');
        }
    }
    
    switch ($paramType) {
        case PARAM_RAW:
            // No cleaning at all.
            $param = fix_utf8($param);
            return $param;
            
        case PARAM_INT:
            // Convert to integer.
            return (int)$param;

        case PARAM_FLOAT:
            // Convert to float.
            return (float)$param;

        case PARAM_ALPHA:
            // Remove everything not `a-z`.
            return preg_replace('/[^a-zA-Z]/i', '', $param);

        case PARAM_ALPHAEXT:
            // Remove everything not `a-zA-Z_-` (originally allowed "/" too).
            return preg_replace('/[^a-zA-Z_-]/i', '', $param);

        case PARAM_ALPHANUM:
            // Remove everything not `a-zA-Z0-9`.
            return preg_replace('/[^A-Za-z0-9]/i', '', $param);

        case PARAM_ALPHANUMEXT:
            // Remove everything not `a-zA-Z0-9_-`.
            return preg_replace('/[^A-Za-z0-9_-]/i', '', $param);
            
        case PARAM_BOOL:
            // Convert to 1 or 0.
            $tempstr = strtolower($param);
            if ($tempstr === 'on' or $tempstr === 'yes' or $tempstr === 'true') {
                $param = 1;
            } else if ($tempstr === 'off' or $tempstr === 'no'  or $tempstr === 'false') {
                $param = 0;
            } else {
                $param = empty($param) ? 0 : 1;
            }
            return $param;
    }
}

/**
 * 
 * @staticvar type $buggyiconv
 * @param type $value
 * @return type
 */
function fix_utf8($value) {
    if (is_null($value) or $value === '') {
        return $value;

    } else if (is_string($value)) {
        if ((string)(int)$value === $value) {
            // Shortcut.
            return $value;
        }
        // No null bytes expected in our data, so let's remove it.
        $value = str_replace("\0", '', $value);

        // Note: this duplicates min_fix_utf8() intentionally.
        static $buggyiconv = null;
        if ($buggyiconv === null) {
            $buggyiconv = (!function_exists('iconv') or @iconv('UTF-8', 'UTF-8//IGNORE', '100'.chr(130).'€') !== '100€');
        }

        if ($buggyiconv) {
            if (function_exists('mb_convert_encoding')) {
                $subst = mb_substitute_character();
                mb_substitute_character('');
                $result = mb_convert_encoding($value, 'utf-8', 'utf-8');
                mb_substitute_character($subst);

            } else {
                // Warn admins on admin/index.php page.
                $result = $value;
            }

        } else {
            $result = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
        }

        return $result;

    } else if (is_array($value)) {
        foreach ($value as $k => $v) {
            $value[$k] = fix_utf8($v);
        }
        return $value;

    } else if (is_object($value)) {
        // Do not modify original.
        $value = clone($value);
        foreach ($value as $k => $v) {
            $value->$k = fix_utf8($v);
        }
        return $value;

    } else {
        // This is some other type, no utf-8 here.
        return $value;
    }
}
