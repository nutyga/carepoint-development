<?php

//------- MENU PAGES ---------//


function register_my_menus() {
  register_nav_menus(
	array(
	  'primary_menu' => __( 'Primary Menu' ),
	  'header_menu' => __( 'Header Menu' ),
	  'footer_menu' => __( 'Footer Menu' )
	)
  );
}

add_action( 'init', 'register_my_menus' );



//------- CUSTOM POST TYPES ---------//


// Create Post Type - Care advice 
function care_advice() {

	$labels = array(
		'name'                => _x( 'Care Advice', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Care Advice', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Care Advice', 'text_domain' ),
		'name_admin_bar'      => __( 'Care Advice', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Items', 'text_domain' ),
		'add_new_item'        => __( 'Add New Item', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Item', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'Care Advice', 'text_domain' ),
		'description'         => __( 'Articles for all the care advice', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', 'page-attributes', ),
		'taxonomies'          => array( 'care-advice-categories' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 8,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,		
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'care-advice', $args );

}
add_action( 'init', 'care_advice', 0 );


// Create Post Type - Care services Directory 
function care_services() {

	$labels = array(
		'name'                => _x( 'Care Services', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'Care Service', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'Care Services', 'text_domain' ),
		'name_admin_bar'      => __( 'Care Services', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Items', 'text_domain' ),
		'add_new_item'        => __( 'Add New Item', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Item', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
	);
	$args = array(
		'label'               => __( 'Care Service', 'text_domain' ),
		'description'         => __( 'Care services directory', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields', 'page-attributes', ),
		'taxonomies'          => array( 'care-services-categories' ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,		
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
	);
	register_post_type( 'care-services', $args );

}
add_action( 'init', 'care_services', 0 );

// Create new Care Advice Taxonomy
function care_advice_categories() {

	$labels = array(
		'name'                       => _x( 'Advice categories', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Advice category', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Care advice categories', 'text_domain' ),
		'all_items'                  => __( 'All Items', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Item Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Item', 'text_domain' ),
		'edit_item'                  => __( 'Edit Item', 'text_domain' ),
		'update_item'                => __( 'Update Item', 'text_domain' ),
		'view_item'                  => __( 'View Item', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Items', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'care-advice-categories', array( 'care-advice' ), $args );

}
add_action( 'init', 'care_advice_categories', 0 );

function care_advice_taxonomy_filter() {
	global $typenow; // this variable stores the current custom post type
	if( $typenow == 'care-advice' ){ // choose one or more post types to apply taxonomy filter for them if( in_array( $typenow  array('post','games') )
		$taxonomy_names = array('care-advice-categories');
		foreach ($taxonomy_names as $single_taxonomy) {
			$current_taxonomy = isset( $_GET[$single_taxonomy] ) ? $_GET[$single_taxonomy] : '';
			$taxonomy_object = get_taxonomy( $single_taxonomy );
			$taxonomy_name = strtolower( $taxonomy_object->labels->name );
			$taxonomy_terms = get_terms( $single_taxonomy );
			if(count($taxonomy_terms) > 0) {
				echo "<select name='$single_taxonomy' id='$single_taxonomy' class='postform'>";
				echo "<option value=''>All $taxonomy_name</option>";
				foreach ($taxonomy_terms as $single_term) {
					echo '<option value='. $single_term->slug, $current_taxonomy == $single_term->slug ? ' selected="selected"' : '','>' . $single_term->name .' (' . $single_term->count .')</option>'; 
				}
				echo "</select>";
			}
		}
	}
}
 
add_action( 'restrict_manage_posts', 'care_advice_taxonomy_filter' );

function add_taxonomies_to_pages() {
 register_taxonomy_for_object_type( 'post_tag', 'page' );
 register_taxonomy_for_object_type( 'category', 'page' );
 }
add_action( 'init', 'add_taxonomies_to_pages' );


// Create new Care Services Taxonomy
function care_services_categories() {

	$labels = array(
		'name'                       => _x( 'Care services categories', 'Taxonomy General Name', 'text_domain' ),
		'singular_name'              => _x( 'Care services category', 'Taxonomy Singular Name', 'text_domain' ),
		'menu_name'                  => __( 'Care services categories', 'text_domain' ),
		'all_items'                  => __( 'All Items', 'text_domain' ),
		'parent_item'                => __( 'Parent Item', 'text_domain' ),
		'parent_item_colon'          => __( 'Parent Item:', 'text_domain' ),
		'new_item_name'              => __( 'New Item Name', 'text_domain' ),
		'add_new_item'               => __( 'Add New Item', 'text_domain' ),
		'edit_item'                  => __( 'Edit Item', 'text_domain' ),
		'update_item'                => __( 'Update Item', 'text_domain' ),
		'view_item'                  => __( 'View Item', 'text_domain' ),
		'separate_items_with_commas' => __( 'Separate items with commas', 'text_domain' ),
		'add_or_remove_items'        => __( 'Add or remove items', 'text_domain' ),
		'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
		'popular_items'              => __( 'Popular Items', 'text_domain' ),
		'search_items'               => __( 'Search Items', 'text_domain' ),
		'not_found'                  => __( 'Not Found', 'text_domain' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => true,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'care-services-categories', array( 'care-services' ), $args );

}
add_action( 'init', 'care_services_categories', 0 );



//------ WIDGETS ------//

function carepoint_widgets() {

	register_sidebar( array(
		'name'          => 'Homepage blocks',
		'id'            => 'home_right_1',
		'before_widget' => '<div>',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="rounded">',
		'after_title'   => '</h2>',
	) );

}
add_action( 'widgets_init', 'carepoint_widgets' );



//------ IMAGE SIZES ------//

add_image_size( 'landscape-4x3', 600, 450 );
add_image_size( 'landscape-16x9', 1280, 720 );
add_image_size( 'square', 600, 600, array( 'left', 'top' ) );



//------ BREAD CRUMBS -------//

function get_taxonomy_parents($parent, $taxonomy)
{
	$parent_tax = get_term( $parent, $taxonomy );
	$parent_tax_link = get_term_link( $parent_tax );

	if($parent_tax->parent != 0)
	{
		get_taxonomy_parents($parent_tax->parent, $parent_tax->taxonomy);
	}

	echo '<li><a href="'.$parent_tax_link.'">'.$parent_tax->name.'</a></li>';
}


function the_breadcrumb()
{
	echo '<nav class="breadcrumbs">';
	echo '	<ul>';
	if(!is_home())
	{

		echo '<li><a href="'.get_option('home').'">Home</a></li>';

		if(is_tax())
		{

			$current_tax = get_queried_object();

				// check if it has a parent
				if($current_tax->parent != 0)
				{
					get_taxonomy_parents($current_tax->parent, $current_tax->taxonomy);					
				}

			// Add the current term last
			echo '<li>'.$current_tax->name.'</li>';

		}

		// if Single

		if(is_single())
		{
			global $post;
			//get the term of the current post
			$post_type = get_post_type( $post );
			//echo get_the_term( $post->ID, 'care-advice' );
			//$term = get_the_term_list( $post->ID, $taxonomy, $before, $sep, $after );
			echo '<li>'.the_title().'</li>';			
		}

	}
	echo '	</ul>';
	echo '</nav>';
}

// add_action('wp_loaded', function(){
//         $post_types = get_post_types( array( 'public' => true ), 'names' ); 
//         print_r($post_types);
// });

//------ DEBUGGING ------//

// function inspect_wp_query() 
// {
//   echo '<pre>';
//     print_r($GLOBALS['wp_query']);
//   echo '</pre>';
// }

// // If you're looking at other variables you might need to use different hooks
// // this can sometimes be a little tricky.
// // Take a look at the Action Reference: http://codex.wordpress.org/Plugin_API/Action_Reference
// add_action( 'shutdown', 'inspect_wp_query', 999 ); // Query on public facing pages
// add_action( 'admin_footer', 'inspect_wp_query', 999 ); // Query in admin UI

function printme($array)
{
	echo '<pre>';
		print_r($array);
	echo '</pre>';	
}

error_reporting(E_ALL);
ini_set('display_errors', '1');
