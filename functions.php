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


/**
 * get parent menu item ID
 * http://wordpress.stackexchange.com/questions/138526/getting-parent-page-id-when-using-custom-menu
 */
if(!function_exists('get_menu_parent_id')){
    function get_menu_parent_id($menu, $post_id = NULL){
        $post_id   = $post_id ? : get_the_ID();
        $locations = get_nav_menu_locations();
        if(isset($locations[$menu])){
            $menu_items     = wp_get_nav_menu_items($locations[$menu]);
            $parent_item_id = wp_filter_object_list($menu_items, array('object_id' => $post_id), 'and', 'menu_item_parent');

            if(!empty($parent_item_id)){
                $parent_item_id = array_shift($parent_item_id);
                $parent_post_id = wp_filter_object_list($menu_items, array('ID' => $parent_item_id), 'and', 'object_id');

                if(!empty($parent_post_id)){
                    return array_shift($parent_post_id);
                }
            }
        }
        return false;
    } //EF
}


/**
 * get ancestor menu item ID
 */
if(!function_exists('get_menu_ancestor_id')){
    function get_menu_ancestor_id($menu, $post_id = NULL){
        $post_id   = $post_id ? : get_the_ID();
        $locations = get_nav_menu_locations();

        if(isset($locations[$menu])){
            $menu_items     = wp_get_nav_menu_items($locations[$menu]);
            $parent_item_id = wp_filter_object_list($menu_items, array('object_id' => $post_id), 'and', 'menu_item_parent');

            if(!empty($parent_item_id)){
                $parent_item_id = array_shift($parent_item_id);

                if(intval($parent_item_id) !== 0){
                    $parent_post_id = wp_filter_object_list($menu_items, array('ID' => $parent_item_id), 'and', 'object_id');

                    if(!empty($parent_post_id)){
                        return get_menu_ancestor_id($menu, array_shift($parent_post_id));
                    }
                } else {
                    return $post_id;
                }
            }
        }
        return false;
    } //EF
}


/**
 * get parent menu item title
 */
if(!function_exists('get_menu_parent_title')){
    function get_menu_parent_title($menu, $post_id = NULL){
        $parent_id = get_menu_parent_id($menu, $post_id);
        return ($parent_id) ? get_the_title($parent_id) : false;
    } //EF
}


/**
 * get ancestor menu item title
 */
if(!function_exists('get_menu_ancestor_title')){
    function get_menu_ancestor_title($menu, $post_id = NULL){
        $parent_id = get_menu_ancestor_id($menu, $post_id);
        return ($parent_id) ? get_the_title($parent_id) : false;
    } //EF
}


/**
 * Check if post is in a menu
 */
if(!function_exists('page_is_in_menu')){
    function page_is_in_menu($menu = null, $object_id = null){
        $menu_object = wp_get_nav_menu_items(esc_attr($menu));
        if(!$menu_object) return false;
        // get the object_id field out of the menu object
        $menu_items = wp_list_pluck($menu_object, 'object_id');
        // use the current post if object_id is not specified
        if(!$object_id){
            global $post;
            $object_id = get_queried_object_id();
        }
        return in_array((int) $object_id, $menu_items);
    } //EF
}


/**
 * Check if post is in taxonomy
 */
if(!function_exists('post_in_taxonomy')){
    function post_in_taxonomy($taxonomy, $post_id = NULL){
        $post_id = $post_id ? : get_the_ID();
        $terms   = get_the_terms($post_id, $taxonomy);
        if(is_array($terms) && count($terms) > 0){
            return array_shift(array_values($terms));
        }
        return FALSE;
    } //EF
}

