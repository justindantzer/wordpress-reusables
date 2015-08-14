<?php // CUSTOM LOGIN


/**
 * Custom login page for theme
 */
class customize_wp_login {

    function __construct(){
        add_action('login_head', array($this, 'login_head'));
        add_filter('login_headerurl', array($this, 'login_headerurl'));
        add_filter('login_headertitle', array($this, 'login_headertitle'));
        // add_filter('login_redirect', array($this, 'login_redirect'), 10, 3);
    }

    /* Add to login header */
    function login_head(){
        echo "<style>\n" .
            ".login h1 a {\n" .
                "\n" .
            "}\n" .
            "p#backtoblog {\n" .
                "display: none;\n" .
            "}\n" .
        "</style>\n";

    }

    /* Set header link URL to current site home */
    function login_headerurl(){
        return get_bloginfo('url');
    }

    /* Set header link title to current site name */
    function login_headertitle(){
        return get_bloginfo('name');
    }

    /* Alter login redirect */
    function login_redirect($redirect_to, $request, $user){
        global $user;
        if(isset($user->roles) && is_Array($user->roles)){
            if(!in_array('administrator', $user->roles)){
                return home_url();
            }
        }
        return $redirect_to;
    }
} //EC
new customize_wp_login;

