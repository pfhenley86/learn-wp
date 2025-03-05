<?php
/**
 * Plugin Name: Bookstore
 * Description: A plugin to manage books
 * Version: 1.0
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Register Custom Post Type Meta
add_action( 'init', 'wp_learn_register_meta' );
function wp_learn_register_meta(){
    register_meta(
        'post',
        'location',
        array(
            'single'       => true,
            'type'         => 'string',
            'default'      => '',
            'show_in_rest' => true,
        )
    );
}

// Register Book Post Type
add_action( 'init', 'bookstore_register_book_post_type' );
function bookstore_register_book_post_type() {
	$args = array(
		'labels'       => array(
			'name'          => 'Books',
			'singular_name' => 'Book',
			'menu_name'     => 'Books',
			'add_new'       => 'Add New Book',
			'add_new_item'  => 'Add New Book',
			'new_item'      => 'New Book',
			'edit_item'     => 'Edit Book',
			'view_item'     => 'View Book',
			'all_items'     => 'All Books',
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'custom-fields' ),
	);

	register_post_type( 'book', $args );

	register_meta(
        'post',
        'isbn',
        array(
            'single'         => true,
            'type'           => 'string',
            'default'        => '',
            'show_in_rest'   => true,
            'object_subtype' => 'book',
        )
    );
}

// Register Genre Taxonomy
add_action( 'init', 'bookstore_register_genre_taxonomy' );
function bookstore_register_genre_taxonomy() {
	$args = array(
		'labels'       => array(
			'name'          => 'Genres',
			'singular_name' => 'Genre',
			'edit_item'     => 'Edit Genre',
			'update_item'   => 'Update Genre',
			'add_new_item'  => 'Add New Genre',
			'new_item_name' => 'New Genre Name',
			'menu_name'     => 'Genre',
		),
		'hierarchical' => true,
		'rewrite'      => array( 'slug' => 'genre' ),
		'show_in_rest' => true,
		'rest_base'    => 'genres',
	);

	register_taxonomy( 'genre', 'book', $args );
}

// Add ISBN to Quick Edit
add_filter( 'postmeta_form_keys', 'bookstore_add_isbn_to_quick_edit', 10, 2 );
function bookstore_add_isbn_to_quick_edit( $keys, $post ) {
	if ( 'book' === $post->post_type ) {
		$keys[] = 'isbn';
	}
	return $keys;
}

// SubMenu
add_action( 'admin_menu', 'bookstore_add_booklist_submenu', 11 );
function bookstore_add_booklist_submenu() {
    add_submenu_page(
        'edit.php?post_type=book',
        'Book List',
        'Book List',
        'edit_posts',
        'book-list',
        'bookstore_render_booklist'
    );
}

// Render Book List
function bookstore_render_booklist() {
    ?>
    <div class="wrap" id="bookstore-booklist-admin">
        <h1>Actions</h1>
        <button id="bookstore-load-books">Load Books</button>
		<button id="bookstore-fetch-books">Fetch Books</button>
        <h2>Books</h2>
        <textarea id="bookstore-booklist" cols="125" rows="15"></textarea>
    </div>
	<div style="width:50%;">
    <h2>Add Book</h2>
		<form>
			<div>
				<label for="bookstore-book-title">Book Title</label>
				<input type="text" id="bookstore-book-title" placeholder="Title">
			</div>
			<div>
				<label for="bookstore-book-content">Book Content</label>
				<textarea id="bookstore-book-content" cols="100" rows="10"></textarea>
			</div>
			<div>
				<input type="button" id="bookstore-submit-book" value="Add">
			</div>
		</form>
	</div>
    <?php
}

// Enqueue Scripts
add_action( 'wp_enqueue_scripts', 'bookstore_enqueue_scripts' );
function bookstore_enqueue_scripts() {
	$post = get_post();
	if ( 'book' !== $post->post_type ) {
		return;
	}
	wp_enqueue_style(
		'bookstore-style',
		plugins_url() . '/bookstore/bookstore.css'
	);
	wp_enqueue_script(
		'bookstyle-script',
		plugins_url() . '/bookstore/bookstore.js'
	);
}

// Enqueue Bookstore Admin Scripts
add_action( 'admin_enqueue_scripts', 'bookstore_admin_enqueue_scripts' );
function bookstore_admin_enqueue_scripts() {
    wp_enqueue_script(
        'bookstore-admin',
        plugins_url() . '/bookstore/admin_bookstore.js',
        array('wp-api', 'wp-api-fetch'),
        '1.0.0',
        true
    );
}

// Add Custom Field Before Meta
add_action( 'rest_api_init', 'bookstore_add_rest_fields' );
function bookstore_add_rest_fields() { 
	register_rest_field(
        'book',
        'isbn',
        array(
            'get_callback'    => 'bookstore_rest_get_isbn',
            'update_callback' => 'bookstore_rest_update_isbn',
            'schema'          => array(
                'description' => __( 'The ISBN of the book' ),
                'type'        => 'string',
            ),
        )
    );
}

function bookstore_rest_get_isbn($book){
	return get_post_meta($book['id'], 'isbn', true);
}

function bookstore_rest_update_isbn($value, $book){
	return update_post_meta($book->ID, 'isbn', $value);
}