<?php

class core_renderer {
    
    public function core_doctype() {
        return "<!DOCTYPE html>\n";
    }
        
    public function core_head() {
        global $CFG;
        $output = '<html>';
        $output .= '<head>';
        $output .= '<meta charset="UTF-8" />';
        $output .= '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />';
        $output .= '<meta name="description" content="BackOffice Capricorne Systèmes" />';
        $output .= '<meta name="keywords" content="Backoffice Capricorne Systèmes, webcs" />';
        $output .= '<meta name="author" content="Simon Camilotti, Webcs, http://www.camilotti.fr" />';
        $output .= '<link rel="icon" type="image/png" href="' . $CFG->wwwroot . '/pix/' . 'favicon.ico" />';
        $output .= '<!--[if IE]><link rel="shortcut icon" type="image/x-icon" href="' . $CFG->wwwroot . '/pix/' . 'favicon.ico" /><![endif]-->';
        return $output;
    }

    
    
    
    public function menu() {
        global $CFG;
        
        $output = '';
        $output .= '<ul>';
        $output .= '<li><a href="' . $CFG->wwwroot . '/view/' . '">View</a></li>';
        $output .= '<li><a href="' . $CFG->wwwroot . '/users/profile/view/' . '">User profile view</a></li>';
        $output .= '<li><a href="' . $CFG->wwwroot . '/test/' . '">Test</a></li>';
        $output .= '</ul>';
        
        return $output;
    }
    
    
    public function test() {
        print_object('toto');
    }
    
}
