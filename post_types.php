<?php // CUSTOM POST-TYPE EXAMPLE


add_action('init', 'add_custom_post_types');
function add_custom_post_types(){

    $taxonomies = array(
        'product_category' => array(
            'post_types'   => 'product',
            'plural'       => 'Product Categories',
            'singular'     => 'Product Category',
            'hierarchical' => true
        ),
        'classification' => array(
            'post_types'   => 'product',
            'plural'       => 'Classifications',
            'singular'     => 'Classification',
            'hierarchical' => false
        )
    );

    foreach($taxonomies as $t => $o){
        extract($o);
        // https://codex.wordpress.org/Function_Reference/register_taxonomy
        register_taxonomy(
            $t,
            $post_types,
            array(
                'labels' => array(
                    'name'                  => _x($plural, 'taxonomy general name'),
                    'singular_name'         => _x($singular, 'taxonomy singular name'),
                    'all_items'             => __('All '.$plural),
                    'edit_item'             => __('Edit '.$singular),
                    'view_item'             => __('View '.$singular),
                    'update_item'           => __('Update '.$singular),
                    'add_new_item'          => __('Add New '.$singular),
                    'new_item_name'         => __('New '.$singular.' Name'),
                    'search_items'          => __('Search '.$plural),
                    'add_or_remove_items'   => __('Add or remove '.strtolower($plural)),
                    'choose_from_most_used' => __('Choose from most used '.strtolower($plural)),
                    'not_found'             => __('No '. strtolower($plural).' found')
                ),
                'hierarchical'      => $hierarchical,
                'public'            => true,
                'show_admin_column' => true
            )
        );
    }

    // https://codex.wordpress.org/Function_Reference/register_post_type
    register_post_type('product',
        array(
            'labels'        => array(
                'name'          => __('Products', THEME_DOMAIN),
                'singular_name' => __('Product', THEME_DOMAIN),
                'add_new_item'  => __('Add New Product', THEME_DOMAIN),
                'view_item'     => __('View Product', THEME_DOMAIN),
                'edit_item'     => __('Edit Product', THEME_DOMAIN)
            ),
            'description'   => '',
            'public'        => TRUE,
            'menu_position' => 5,
            'menu_icon'     => 'dashicons-products',
            'has_archive'   => TRUE,
            'taxonomies'    => array('product_category', 'classification'),
            'supports'      => array(
                'title',
                'editor',
                // 'author',
                'thumbnail',
                'excerpt',
                // 'trackbacks',
                'custom-fields',
                // 'comments',
                'revisions',
                'page-attributes',
                // 'post-formats'
            ),
            'rewrite'       => array(
                'slug' => 'products'
            )
        )
    );

}

