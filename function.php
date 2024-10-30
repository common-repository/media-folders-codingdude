<?php
/*
Plugin Name: CodingDude Media Folders by CodingDude
Description: Add folders for media items
Author: John Negoita (@codingdudecom)
Author URI: http://www.coding-dude.com
Version: 1.1
License: GPL2
Text Domain: media library
Domain Path: Domain Path
*/

/*

    Copyright (C) Year  John Negoita (@codingdudecom)  Email

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


add_action( 'init', 'codingdudecom_mediafolders_create_book_taxonomies', 0 );

// create two taxonomies, genres and writers for the post type "book"
function codingdudecom_mediafolders_create_book_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x( 'Element categories', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'Element category', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'Search Element Categories', 'textdomain' ),
        'all_items'         => __( 'All Element Categories', 'textdomain' ),
        'parent_item'       => __( 'Parent Element Category', 'textdomain' ),
        'parent_item_colon' => __( 'Parent Element Category:', 'textdomain' ),
        'edit_item'         => __( 'Edit Element Category', 'textdomain' ),
        'update_item'       => __( 'Update Element Category', 'textdomain' ),
        'add_new_item'      => __( 'Add New Element Category', 'textdomain' ),
        'new_item_name'     => __( 'New Element Category Name', 'textdomain' ),
        'menu_name'         => __( 'Element Category', 'textdomain' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'elements' ),
    );

    register_taxonomy( 'element-category', array( 'attachment' ), $args );
}


function codingdudecom_mediafolders_media_add_author_dropdown()
{
    $scr = get_current_screen();
    if ( $scr->base !== 'upload' ) return;

    $author   = filter_input(INPUT_GET, 'author', FILTER_SANITIZE_STRING );
    $selected = (int)$author > 0 ? $author : '-1';
    $args = array(
        'show_option_none'   => 'All Authors',
        'name'               => 'author',
        'selected'           => $selected
    );
    wp_dropdown_users( $args );

    $categ   = filter_input(INPUT_GET, 'element-category', FILTER_SANITIZE_STRING );
    $selected = (int)$categ > 0 ? $categ : '-1';
    $args = array(
        'show_option_none'   => 'All Categories',
        'name'               => 'element-category',
        'selected'           => $selected,
        'taxonomy'           => 'element-category',
        'hide_empty'        => 0
    );
    wp_dropdown_categories( $args );    
}
add_action('restrict_manage_posts', 'codingdudecom_mediafolders_media_add_author_dropdown');

function codingdudecom_mediafolders_author_filter($query) {
    if ( is_admin() && $query->is_main_query() ) {
        if (isset($_GET['author']) && $_GET['author'] == -1) {
            $query->set('author', '');
        }
        
        if (isset($_GET['element-category'])){
            if ($_GET['element-category'] == -1) {
                $query->set('element-category', '');
            }else {
                $term = get_term_by('id',(int)$_GET['element-category'],'element-category' );
                $query->set('element-category', $term->name);
            }
        }
    }
}
add_action('pre_get_posts','codingdudecom_mediafolders_author_filter');