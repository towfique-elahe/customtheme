<?php
/**
 * Function File Name: Theme Support Functions
 * 
 * The file for theme support functions.
 */

// Register theme support features
function customtheme_advanced_theme_support() {
    // Enable custom logo support with specific dimensions and flexibility
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 300,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Enable dynamic title tag support
    add_theme_support('title-tag');

    // Enable post thumbnails (featured images)
    add_theme_support('post-thumbnails');

    // Add custom image sizes
    add_image_size('custom-thumbnail', 600, 400, true);  // 600x400 crop mode
    add_image_size('hero-image', 1920, 800, true);       // 1920x800 crop mode

    // Enable WooCommerce support
    add_theme_support('woocommerce');

    // Enable HTML5 markup support for various elements
    add_theme_support('html5', array(
        'comment-list',
        'comment-form',
        'search-form',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add support for selective refresh in the customizer
    add_theme_support('customize-selective-refresh-widgets');

    // Enable support for editor styles and load a custom editor stylesheet
    add_editor_style('editor-style.css');

    // Enable custom background support
    add_theme_support('custom-background', array(
        'default-color' => 'ffffff',
        'default-image' => '',
    ));

    // Add theme support for block styles (Gutenberg)
    add_theme_support('wp-block-styles');

    // Add wide and full alignment support for Gutenberg blocks
    add_theme_support('align-wide');

    // Add support for responsive embedded content
    add_theme_support('responsive-embeds');
	
	// Add support for widgets
	add_theme_support( 'widgets' );
}
add_action('after_setup_theme', 'customtheme_advanced_theme_support');

/**
 * Add Elementor Support
 */
function customtheme_add_elementor_support() {
    // Ensure Elementor can work with your theme
    add_theme_support('elementor');

    // Register locations for Elementor Theme Builder (e.g., header, footer)
    if (class_exists('Elementor\ThemeManager')) {
        add_action('elementor/theme/register_locations', function($elementor_theme_manager) {
            $elementor_theme_manager->register_all_core_location();
        });
    }

    // Enable custom breakpoints for Elementor if needed
    add_theme_support('elementor-custom-breakpoints');
}
add_action('after_setup_theme', 'customtheme_add_elementor_support');

/**
 * Menu Registration and Custom Menu Functions
 */

// Register theme menus
function customtheme_register_menus() {
    register_nav_menus([
        'primary-menu'   => __('Primary Menu', 'customtheme'),
        'footer-menu-1'  => __('Footer Menu 1', 'customtheme'),
        'footer-menu-2'  => __('Footer Menu 2', 'customtheme'),
        'footer-menu-3'  => __('Footer Menu 3', 'customtheme'),
        'mobile-menu'    => __('Mobile Menu', 'customtheme'),
    ]);
}
add_action('init', 'customtheme_register_menus');

/**
 * Display a fallback menu when no menu is assigned.
 */
function customtheme_fallback_menu() {
    echo '<ul class="fallback-menu">';
    echo '<li><a href="' . esc_url(home_url('/')) . '">' . __('Home', 'customtheme') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/about')) . '">' . __('About', 'customtheme') . '</a></li>';
    echo '<li><a href="' . esc_url(home_url('/contact')) . '">' . __('Contact', 'customtheme') . '</a></li>';
    echo '</ul>';
}

/**
 * Custom Walker for Nav Menus (for adding custom classes and structure).
 */
class customtheme_Custom_Nav_Walker extends Walker_Nav_Menu {
    // Start level (for submenus)
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }

    // Start element (for menu items)
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= '<li' . $class_names . '>';

        $attributes  = !empty($item->attr_title) ? ' title="' . esc_attr($item->attr_title) . '"' : '';
        $attributes .= !empty($item->target) ? ' target="' . esc_attr($item->target) . '"' : '';
        $attributes .= !empty($item->xfn) ? ' rel="' . esc_attr($item->xfn) . '"' : '';
        $attributes .= !empty($item->url) ? ' href="' . esc_attr($item->url) . '"' : '';

        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= $item_output;
    }
}

/**
 * Display a menu with optional fallback and custom walker.
 *
 * @param string $theme_location The registered menu location.
 */
function customtheme_display_menu($theme_location) {
    if (has_nav_menu($theme_location)) {
        wp_nav_menu([
            'theme_location' => $theme_location,
            'container'      => 'nav',
            'container_class'=> 'customtheme-nav',
            'menu_class'     => 'customtheme-menu',
            'fallback_cb'    => 'customtheme_fallback_menu',
            'walker'         => new customtheme_Custom_Nav_Walker(),
        ]);
    } else {
        customtheme_fallback_menu();
    }
}

/**
 * Registered widget area
 */
function customtheme_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Main Sidebar', 'customtheme' ),
        'id'            => 'sidebar-1',
        'description'   => __( 'Widgets in this area will be shown on the sidebar.', 'customtheme' ),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'customtheme_widgets_init' );


/**
 * Register Custom Post Type: Service
 */
function customtheme_register_service_post_type() {
    $labels = array(
        'name'                  => _x('Services', 'Post Type General Name', 'customtheme'),
        'singular_name'         => _x('Service', 'Post Type Singular Name', 'customtheme'),
        'menu_name'             => __('Services', 'customtheme'),
        'name_admin_bar'        => __('Service', 'customtheme'),
        'archives'              => __('Service Archives', 'customtheme'),
        'attributes'            => __('Service Attributes', 'customtheme'),
        'parent_item_colon'     => __('Parent Service:', 'customtheme'),
        'all_items'             => __('All Services', 'customtheme'),
        'add_new_item'          => __('Add Service', 'customtheme'),
        'add_new'               => __('Add New', 'customtheme'),
        'new_item'              => __('New Service', 'customtheme'),
        'edit_item'             => __('Edit Service', 'customtheme'),
        'update_item'           => __('Update Service', 'customtheme'),
        'view_item'             => __('View Service', 'customtheme'),
        'view_items'            => __('View Services', 'customtheme'),
        'search_items'          => __('Search Service', 'customtheme'),
        'not_found'             => __('Not service found', 'customtheme'),
        'not_found_in_trash'    => __('Not service found in Trash', 'customtheme'),
        'featured_image'        => __('Featured Image', 'customtheme'),
        'set_featured_image'    => __('Set featured image', 'customtheme'),
        'remove_featured_image' => __('Remove featured image', 'customtheme'),
        'use_featured_image'    => __('Use as featured image', 'customtheme'),
        'insert_into_item'      => __('Insert into service', 'customtheme'),
        'uploaded_to_this_item' => __('Uploaded to this service', 'customtheme'),
        'items_list'            => __('Services list', 'customtheme'),
        'items_list_navigation' => __('Services list navigation', 'customtheme'),
        'filter_items_list'     => __('Filter services list', 'customtheme'),
    );

    $args = array(
        'label'                 => __('Service', 'customtheme'),
        'description'           => __('Custom Post Type for Services', 'customtheme'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions', 'author', 'custom-fields'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-hammer', // Custom icon
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => true,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'show_in_rest'          => true, // Enables Gutenberg support
        'capability_type'       => 'post',
    );

    register_post_type('service', $args);
}
add_action('init', 'customtheme_register_service_post_type');


/**
 * Add custom meta boxes to the 'service' post type.
 */
function customtheme_add_service_meta_boxes() {
    add_meta_box(
        'service_details_meta_box',       // ID
        __('Service Details', 'customtheme'), // Title
        'customtheme_render_service_meta_box', // Callback
        'service',                        // Post type
        'normal',                         // Context
        'high'                            // Priority
    );
}
add_action('add_meta_boxes', 'customtheme_add_service_meta_boxes');


/**
 * Render fields inside the meta box.
 */
function customtheme_render_service_meta_box($post) {
    // Retrieve existing values
    $short_description = get_post_meta($post->ID, '_service_short_description', true);
    $price = get_post_meta($post->ID, '_service_price', true);

    // Security nonce
    wp_nonce_field('customtheme_save_service_meta_box', 'customtheme_service_meta_box_nonce');

    ?>
<p>
    <label for="service_short_description"><strong><?php _e('Short Description', 'customtheme'); ?></strong></label><br>
    <textarea id="service_short_description" name="service_short_description" rows="4"
        style="width:100%;"><?php echo esc_textarea($short_description); ?></textarea>
</p>

<p>
    <label for="service_price"><strong><?php _e('Price', 'customtheme'); ?></strong></label><br>
    <input type="text" id="service_price" name="service_price" value="<?php echo esc_attr($price); ?>"
        style="width:200px;" />
</p>
<?php
}


/**
 * Save the custom meta box data.
 */
function customtheme_save_service_meta_box($post_id) {
    // Security check
    if (!isset($_POST['customtheme_service_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['customtheme_service_meta_box_nonce'], 'customtheme_save_service_meta_box')) {
        return;
    }

    // Prevent autosave from triggering the update
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    // Check user capability
    if (!current_user_can('edit_post', $post_id)) return;

    // Sanitize and save fields
    if (isset($_POST['service_short_description'])) {
        update_post_meta($post_id, '_service_short_description', sanitize_textarea_field($_POST['service_short_description']));
    }

    if (isset($_POST['service_price'])) {
        update_post_meta($post_id, '_service_price', sanitize_text_field($_POST['service_price']));
    }
}
add_action('save_post', 'customtheme_save_service_meta_box');