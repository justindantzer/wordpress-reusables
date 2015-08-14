<?php // CUSTOM WIDGETS


/**
 * Do Shortcodes in Text Widget
 */
add_filter('widget_text', 'do_shortcode');


/**
 * ADD CUSTOM CLASS FIELD FOR ALL WIDGETS
 * Allows addition of classes when wanting to customize a widget
 * Also adds first|last classes to widgets in a sidebar
 */
class custom_widget_classes {

    function custom_widget_classes(){ $this->__construct(); } //EF

    function __construct(){
        add_filter('widget_form_callback', array(&$this, 'form_extend'), 10, 2);
        add_filter('widget_update_callback', array(&$this, 'update_extend'), 10, 3);
        add_filter('dynamic_sidebar_params', array(&$this, 'dynamic_sidebar_params_extend'));
    } //EF

    function form_extend($instance, $widget){
        if(empty($instance['classes'])) $instance['classes'] = null;
        ?>
        <p><label for="widget-<?php echo $widget->id_base.'-'.$widget->number; ?>-classes"><?php _e('Custom Classes: '); ?></label>
            <input class="widefat" id="widget-<?php echo $widget->id_base.'-'.$widget->number; ?>-classes" name="widget-<?php echo $widget->id_base.'['.$widget->number.']'; ?>[classes]" type="text" value="<?php echo $instance['classes']; ?>" /><br />
            <small><em>Specify to add classes to the widget container</em></small></p>
        <?php
        return $instance;
    } //EF

    function update_extend($instance, $new_instance, $old_instance){
        $instance['classes'] = $new_instance['classes'];
        return $instance;
    } //EF

    function dynamic_sidebar_params_extend($params){
        global $wp_registered_widgets, $my_widget_num;
        if(!$my_widget_num) $my_widget_num = array();

        $add_classes = array();

        // add custom field classes
        $widget_id  = $params[0]['widget_id'];
        $widget_obj = $wp_registered_widgets[$widget_id];
        $widget_opt = get_option($widget_obj['callback'][0]->option_name);
        $widget_num = $widget_obj['params'][0]['number'];
        if(!empty($widget_opt[$widget_num]['classes'])){
            $add_classes[] = $widget_opt[$widget_num]['classes'];
        }

        // add first/last class
        $this_id = $params[0]['id'];
        $my_widget_num[$this_id] = (isset($my_widget_num[$this_id])) ? $my_widget_num[$this_id] + 1 : 1;
        if($my_widget_num[$this_id] == 1){
            $add_classes[] = 'widget-first';
        }
        if($my_widget_num[$this_id] == count($arr_registered_widgets[$this_id])){
            $add_classes[] = 'widget-last';
        }

        if(!empty($add_classes)){
            $add_classes = join(' ', $add_classes);
            $params[0]['before_widget'] = preg_replace('/class="([^\"]+)"/i', "class=\"$1 {$add_classes}\"", $params[0]['before_widget'], 1);
        }

        return $params;
    } //EF

} //EC
new custom_widget_classes();
