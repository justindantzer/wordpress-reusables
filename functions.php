<?php // CUSTOM FUNCTIONS


/* DETECT IF ON LOGIN PAGE
 * Simply detect if currently on the login or register pages
 */
if(!function_exists('is_login_page')){
    function is_login_page(){
        return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
    } //EF
}


/**
 * ADD FILTERS TO MULTIPLE ITEMS WITH SAME FUNCTION
 */
if(!function_exists('add_filters')){
    function add_filters($tags, $function){
        foreach($tags as $tag) add_filter($tag, $function);
    } //EF
}
