<?php

/**
 * Dumps a given object's information for debugging purposes
 *
 * When used in a CLI script, the object's information is written to the standard
 * error output stream. When used in a web script, the object is dumped to a
 * pre-formatted block with the "print_notification" CSS class.
 *
 * @param mixed $object The data to be printed
 * @return void output is echo'd
 */
function print_object($object) {
    echo tag('pre', s(print_r($object, true)), array('class' => 'print_notification'));
}

/**
 * Outputs a tag with attributes and contents
 *
 * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
 * @param string $contents What goes between the opening and closing tags
 * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
 * @return string HTML fragment
 */
function tag($tagname, $contents, array $attributes = null) {
    return start_tag($tagname, $attributes) . $contents . end_tag($tagname);
}

/**
 * Outputs an opening tag with attributes
 *
 * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
 * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
 * @return string HTML fragment
 */
function start_tag($tagname, array $attributes = null) {
    return '<' . $tagname . attributes($attributes) . '>';
}

/**
 * Outputs a closing tag
 *
 * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
 * @return string HTML fragment
 */
function end_tag($tagname) {
    return '</' . $tagname . '>';
}

/**
 * Outputs a list of HTML attributes and values
 *
 * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
 *       The values will be escaped with {@link s()}
 * @return string HTML fragment
 */
function attributes(array $attributes = null) {
    $attributes = (array)$attributes;
    $output = '';
    foreach ($attributes as $name => $value) {
        $output .= attribute($name, $value);
    }
    return $output;
}

/**
 * Outputs a HTML attribute and value
 *
 * @param string $name The name of the attribute ('src', 'href', 'class' etc.)
 * @param string $value The value of the attribute. The value will be escaped with {@link s()}
 * @return string HTML fragment
 */
function attribute($name, $value) {
    return ' ' . $name . '="' . s($value) . '"';
}

/**
 * Add quotes to HTML characters
 *
 * Returns $var with HTML characters (like "<", ">", etc.) properly quoted.
 * This function is very similar to {@link p()}
 *
 * @param string $var the string potentially containing HTML characters
 * @return string
 */
function s($var) {
    if ($var === false) {
        return '0';
    }
    return preg_replace('/&amp;#(\d+|x[0-9a-f]+);/i', '&#$1;', htmlspecialchars($var, ENT_QUOTES, 'UTF-8'));
}
