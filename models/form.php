<?php

class form {

    protected $formname;
    protected $method;
    protected $action;
    protected $fields;
    protected $fieldstype;
    protected $hidden_fields;
    protected $submit_fields;
    protected $fieldsets;
    protected $attrs;

    public function __construct($formname, $method = 'POST', $action = '') {
        
        $this->formname = $formname;
        $this->method = $method;
        $this->action = $action;
        $this->fields = array();
        $this->fieldstype = array();
        $this->hidden_fields = array();
        $this->submit_fields = array();
        $this->fieldsets = array();
        $this->attrs = new AttributeList(array('method' => $method));
    }

    public function header() {
        if(!empty($this->action)) {
            $this->action = htmlspecialchars($action);
        }
        
        $header = '<form method="' . $this->method . '" action="' . $this->action . '">';
        $header .= '<input type="hidden" name="formname" value="' . $this->formname . '">';
        return $header;
    }

    public function footer() {
        $footer = '</form>';
        return $footer;
    }

    public function add($fieldtype, $fieldname) {
        $field = 'form_' . strtolower($fieldtype);
        $field_object = new $field($fieldname);
        $this->fields[$fieldname] = $field_object;
        $this->fieldstype[$fieldname] = $field_object;
        return $field_object;
    }

    public function display() {
        echo $this->__toString();
    }

    public function __toString() {
        $form = '';
        $form .= $this->header();

        foreach ($this->fields as $field) {
            $form .= $field;
        }

        $form .= $this->footer();
        return $form;
    }
    
    public function getDatas() {
        if($this->is_submitted()) {
            $this->getCleanDatas();
        }
        return false;
    }
    
    public function getCleanDatas() {
        $datas = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : $_GET;
        $cleandatas = array();
        
        foreach($datas as $name => $value) {
            $cleandata = trim($value);
            $cleandata = stripslashes($value);
            $cleandata = htmlspecialchars($value);
            
            $cleandatas[$name] = $cleandata;
        }
    }
    
    public function is_submitted() {
        $check = ($_SERVER['REQUEST_METHOD'] == 'POST') ? $_POST : $_GET;
        if(!empty($check['formname']) && $check['formname'] == $this->formname) {
            foreach($this->fields as $field) {
                if($field->getRequired()) {
                    if(empty($check[$field->getName()])) {
                        return false;
                    }
                }
            }
            return true;
        }
        return false;
    }
    
}

abstract class form_field {

    protected $required;
    protected $label;
    protected $value;
    protected $attrs;

    public function __construct($name) {
        $this->required = true;
        $this->label = '';
        $this->value = '';
        $this->attrs = new AttributeList;
        $this->attrs['name'] = $name;
    }

    public function required($bool = false) {
        if (true === $bool) {
            $this->attrs['required'] = 'required';
            $this->required = true;
        } else {
            unset($this->attrs['required']);
            $this->required = false;
        }
        return $this;
    }

    public function placeholder($text) {
        $this->attrs['placeholder'] = $text;
        return $this;
    }

    public function label($text) {
        $this->label = $text;
        return $this;
    }

    public function value($text) {
        $this->value = $text;
        return $this;
    }
    
    public function getName() {
        return $this->attrs['name'];
    }
    
    public function getType() {
        return $this->attrs['type'];
    }
    
    public function getValue() {
        return $this->value;
    }
    
    public function getRequired() {
        return $this->required;
    }

    abstract public function __toString();
}

abstract class form_input extends form_field {

    protected $attrs;
    protected $autocomplete;

    public function __construct($name) {
        parent::__construct($name);
        $this->attrs['type'] = 'text';
        $this->autocomplete = true;
    }
    
    public function autocomplete($bool) {
        if (false === $bool) {
            $this->attrs['autocomplete'] = 'off';
            $this->autocomplete = false;
        } else {
            unset($this->attrs['autocomplete']);
            $this->autocomplete = true;
        }
        return $this;
    }

}

class form_text extends form_input {
    
    public function __construct($name) {
        parent::__construct($name);
        $this->attrs['type'] = 'text';
    }

    public function __toString() {
        $label = '';
        $value = '';
        $required = '';

        if (!empty($this->label)) {
            $label = '<label for="' . $this->attrs['name'] . '">' . $this->label . '</label>';
        }

        if (!empty($this->value)) {
            $value = ' value="' . $this->value . '"';
        }

        $field = '<input' . $this->attrs . $value . ' />';

        if ($this->required) {
            $required = '<span class="required">*</span>';
        }
        return $label . $field . $required;
    }

}

class form_email extends form_input {

    public function __construct($name) {
        parent::__construct($name);
        $this->attrs['type'] = 'email';
    }

    public function __toString() {
        $label = '';
        $value = '';
        $required = '';

        if (!empty($this->label)) {
            $label = '<label for="' . $this->attrs['name'] . '">' . $this->label . '</label>';
        }

        if (!empty($this->value)) {
            $value = ' value="' . $this->value . '"';
        }

        $field = '<input' . $this->attrs . $value . ' />';

        if ($this->required) {
            $required = '<span class="required">*</span>';
        }
        return $label . $field . $required;
    }

}

class form_password extends form_input {

    public function __construct($name) {
        parent::__construct($name);
        $this->attrs['type'] = 'password';
    }

    public function __toString() {
        $label = '';
        $value = '';
        $required = '';

        if (!empty($this->label)) {
            $label = '<label for="' . $this->attrs['name'] . '">' . $this->label . '</label>';
        }

        if (!empty($this->value)) {
            $value = ' value="' . $this->value . '"';
        }

        $field = '<input' . $this->attrs . $value . ' />';

        if ($this->required) {
            $required = '<span class="required">*</span>';
        }
        return $label . $field . $required;
    }

}

class form_tel extends form_input {
    
    public function __construct($name) {
        parent::__construct($name);
        $this->attrs['type'] = 'tel';
    }

    public function __toString() {
        $label = '';
        $value = '';
        $required = '';

        if (!empty($this->label)) {
            $label = '<label for="' . $this->attrs['name'] . '">' . $this->label . '</label>';
        }

        if (!empty($this->value)) {
            $value = ' value="' . $this->value . '"';
        }

        $field = '<input' . $this->attrs . $value . ' />';

        if ($this->required) {
            $required = '<span class="required">*</span>';
        }
        return $label . $field . $required;
    }

}


class form_submit extends form_input {
    
    public function __construct($name) {
        parent::__construct($name);
        $this->attrs['type'] = 'submit';
    }
    
    
    public function __toString() {
        $value = '';
        if (!empty($this->value)) {
            $value = ' value="' . $this->value . '"';
        }

        $field = '<input' . $this->attrs . $value . ' />';
        return $field;
    }
}


class form_hidden extends form_input {

    public function __construct($name) {
        parent::__construct($name);
        $this->attrs['type'] = 'hidden';
    }

    public function __toString() {
        $value = '';
        if (!empty($this->value)) {
            $value = ' value="' . $this->value . '"';
        }

        $field = '<input' . $this->attrs . $value . ' />';
        return $field;
    }

}

class ListArray implements Iterator, ArrayAccess {

    protected $array = array();
    private $valid = false;

    function __construct(Array $array = array()) {
        $this->array = $array;
    }

    /* Iterator */

    function rewind() {
        $this->valid = (FALSE !== reset($this->array));
    }

    function current() {
        return current($this->array);
    }

    function key() {
        return key($this->array);
    }

    function next() {
        $this->valid = (FALSE !== next($this->array));
    }

    function valid() {
        return $this->valid;
    }

    /* ArrayAccess */

    public function offsetExists($offset) {
        return isset($this->array[$offset]);
    }

    public function offsetGet($offset) {
        return $this->array[$offset];
    }

    public function offsetSet($offset, $value) {
        return $this->array[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->array[$offset]);
    }

}

// ---------------------------------------------------------------------------
class ErrorList extends ListArray {

    public function as_array() {

        return $this->array;
    }

    public function __toString() {

        $tab = func_num_args() > 0 ? func_get_arg(0) : '';

        if (!empty($this->array)) {

            return sprintf($tab . "<ul>\n\t$tab<li>%s</li>\n$tab</ul>", implode("</li>\n\t$tab<li>", $this->array));
        }
        return '';
    }

}

// ---------------------------------------------------------------------------
class AttributeList extends ListArray {

    public function __toString() {

        $o = '';
        if (!empty($this->array)) {

            foreach ($this->array as $a => $v) {

                $o .= sprintf(' %s="%s"', $a, htmlspecialchars($v));
            }
        }
        return $o;
    }

}
