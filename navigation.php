<?php // CUSTOM NAVIGATION


class customize_wp_nav_menu {

    function __construct(){
        add_filter('wp_nav_menu', array($this, 'wp_nav_menu'));
        add_filter('wp_nav_menu_args', array($this, 'wp_nav_menu_args'));
    }

    /* SIMPLIFY NAVIGATION CLASSES
     * convert current-menu|page|post-item|parent|ancestor to active|active-parent
     */
    function wp_nav_menu($text){
        $text = preg_replace(array('/(current(-menu-|[-_]page[-_]|[-_]post[-_])(parent|ancestor))/', '/current(-menu-|[-_]page[-_]|[-_]post[-_])item/'), array('active-parent', 'active'), $text);
        $text = preg_replace(array('/( active(?!-parent)){2,}/', '/( active-parent){2,}/'), array(' active', ' active-parent'), $text);
        $text = preg_replace('/(menu-item-has-children)/', 'global-nav-li-has-children', $text);
        return $text;
    }

    /* ALTER WP_NAV_MENU ARGUMENTS
     * add new default arguments
     */
    function wp_nav_menu_args($args){
        $add = array('sub_menu_class', 'element_class', 'link_class', 'show_parent');
        return array_merge(array_flip($add), $args);
    } //EF
}
new customize_wp_nav_menu;


/**
 * SIMPLIFY NAVIGATION OUTPUT
 * help simplify navigation output
 * http://benword.com/how-to-hide-that-youre-using-wordpress/
 */
class custom_simplify_walker extends Walker_Nav_Menu {

    var $found_items = array();

    function check_current($classes){
        return preg_match('/(current[-_])|(has-children)/', $classes);
    } //EF

    function start_el(&$output, $item, $depth, $args){

        $indent = ($depth) ? str_repeat("\t", $depth) : '';

        $this->found_items[] = $item->ID;

        $slug = sanitize_title($item->title);
        $id   = apply_filters('nav_menu_item_id', 'menu-' . $slug, $item, $args);
        $id   = (strlen($id) && $id != 'menu-') ? '' . esc_attr( $id ) . '' : '';

        $class_names = $value = '';
        $classes     = empty($item->classes) ? array() : (array) $item->classes;
        $classes     = array_filter($classes, array(&$this, 'check_current'));

        if($custom_classes = get_post_meta($item->ID, '_menu_item_classes', true)){
            foreach($custom_classes as $custom_class){
                $classes[] = $custom_class;
            }
        }

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = " class=\"{$args->element_class} {$id} " . ($class_names ? esc_attr($class_names) : '') . "\"";

        $output .= $indent . '<li' . $class_names . '>';

        if(in_array($item->title, array('|', '-'))){
            $item_output = $item->title;
        } else {
            $attributes  = "";
            $attributes .= !empty($args->link_class) ? ' class="'  . esc_attr($args->link_class) .'"' : '';
            $attributes .= !empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
            $attributes .= !empty($item->target)     ? ' target="' . esc_attr($item->target    ) .'"' : '';
            $attributes .= !empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn       ) .'"' : '';
            $attributes .= !empty($item->url)        ? ' href="'   . esc_attr($item->url       ) .'"' : '';

            $item_output  = $args->before;
            $item_output .= '<a'. $attributes .'>';
            $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
            $item_output .= '</a>';
            $item_output .= $args->after;
        }

        $output      .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    } //EF

    function start_lvl(&$output, $depth, $args){
        $indent  = str_repeat("\t", $depth);
        $output .= "\n$indent<ul " . (!empty($args->sub_menu_class) ? "class=\"{$args->sub_menu_class}\"" : '') . ">\n";
    }
} //EC


/**
 * SIMPLIFY SUBNAVIGATION OUTPUT
 * include features from simplify navigation walker
 * http://benword.com/how-to-hide-that-youre-using-wordpress/
 */
class custom_simplify_subnav_walker extends Walker_Nav_Menu {

    var $found_parents = array();
    var $found_items = array();

    function check_current($classes){
        return preg_match('/(current[-_])/', $classes);
    } //EF

    function start_el(&$output, $item, $depth, $args){

        $parent_item_id = 0; //this only works for second level sub navigations
        $indent = ($depth) ? str_repeat( "\t", $depth ) : '';

        $slug = sanitize_title($item->title);
        $id = apply_filters('nav_menu_item_id', 'menu-' . $slug, $item, $args);
        $id = strlen($id) ? '' . esc_attr( $id ) . '' : '';

        $class_names = $value = '';
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
        $class_names = ' class="' . esc_attr($class_names) . '"';

        if($depth != 0 && ($item->menu_item_parent==0) && (strpos($class_names, 'current-menu-parent'))){
            $output.= '<li>';
        }

        // Checks if the current element is in the current selection
        if(preg_match('/(current(-menu-|[-_]page[-_]|[-_]post[-_])(item|parent|ancestor))/', $class_names)
        || (is_array($this->found_parents) && in_array($item->menu_item_parent, $this->found_parents))){

            $this->found_parents[] = $item->ID; // Keep track of all selected parents

            //check if the item_parent matches the current item_parent
            if($item->menu_item_parent != $parent_item_id || $args->show_parent){

                $this->found_items[] = $item->ID;

                // reused from simplify walker
                $classes = array_filter($classes, array(&$this, 'check_current'));
                if($custom_classes = get_post_meta($item->ID, '_menu_item_classes', true)){
                    foreach($custom_classes as $custom_class) $classes[] = $custom_class;
                }

                // unlinked parent class
                if(0 == $depth && !$args->link_parent) $classes = array('parent-title');

                $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
                //$class_names = $class_names ? ' class="' . $id . ' ' . esc_attr($class_names) . '"' : ' class="' . $id . '"';
                $class_names = $class_names ? " class=\"{$args->element_class} {$id} " . esc_attr($class_names) . "\"" : " class=\"{$args->element_class} {$id}\"";
                // end simplify walker reusing

                $output .= $indent . '<li'. $class_names .'>';

                if(0 == $depth && $args->show_parent && !$args->link_parent){
                    $item_output = $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
                } else {
                    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
                    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
                    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
                    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

                    $item_output = $args->before;
                    $item_output .= '<a'. $attributes .'>';
                    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
                    $item_output .= '</a>';
                    $item_output .= $args->after;
                }

                $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

                if(0 == $depth && $args->show_parent) $output .= '</li>';
            }


        }
    } //EF

    function end_el(&$output, $item, $depth, $args){
        // check for only parent item
        $diff = array_diff($this->found_items, $this->found_parents);
        if($args->show_parent && count($this->found_items) < 2 && empty($diff)) $output = '';

        // Closes only the opened li
        if(0 == $depth) return;

        if(is_array($this->found_parents) && in_array($item->ID, $this->found_parents)){
            $output .= "</li>\n";
        }
    } //EF

    function start_lvl(&$output, $depth, $args){
        if($args->show_parent){
            $output;
        } else {
            $indent = str_repeat("\t", $depth);
            $output .= "\n$indent<ul class=\"{$args->sub_menu_class}\">\n";
        }
    } //EF

    function end_lvl(&$output, $depth, $args){
        if($args->show_parent){
            $output;
        } else {
            $indent = str_repeat("\t", $depth);

            // If the sub-menu is empty, strip the opening tag, else closes it
            $output_len = strlen($args->sub_menu_class) + 14;
            if(substr($output, (-1 * $output_len)) == "<ul class=\"{$args->sub_menu_class}\">\n"){
                $output = substr($output, 0, strlen($output) - ($output_len + 1));
            } else {
                $output .= "$indent</ul>\n";
            }
        }
    } //EF

} //EC
