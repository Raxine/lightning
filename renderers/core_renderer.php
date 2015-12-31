<?php

class core_renderer {
    
    function menu() {
        global $CFG;
        $output = '';
        $output .= '<ul>';
        $output .= '<li>';
        $output .= '<a href="' . $CFG->wwwroot . '/users/view?userid=3' . '">Users</a>';
        $output .= '</li>';
        $output .= '<li>';
        $output .= '<a href="' . $CFG->wwwroot . '/messages/view?id=2&cat=4' . '">Messages</a>';
        $output .= '</li>';
        $output .= '<li>';
        $output .= '<a href="' . $CFG->wwwroot . '/view/32' . '">tt</a>';
        $output .= '</li>';
        $output .= '</ul>';
        
        return $output;
    }
    
}
