<?php
//function to search from given post type excluding page
function search_only_title_except_pages( $search, $query ) {
    if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
        global $wpdb;

        $search = '';
        $search_terms = $query->get( 's' );

        if ( ! empty( $search_terms ) ) {
            $search = $wpdb->prepare(
                " AND $wpdb->posts.post_type IN ( 'post', 'news', 'events', 'uas' ) 
                AND $wpdb->posts.post_title LIKE %s",
                '%' . $wpdb->esc_like( $search_terms ) . '%'
            );
        }

        // Exclude the 'page' post type.
        $exclude_page = get_post_type_object( 'page' );
        if ( $exclude_page && isset( $exclude_page->rewrite['slug'] ) ) {
            $exclude_page_slug = $exclude_page->rewrite['slug'];
            $search .= $wpdb->prepare( " AND $wpdb->posts.post_type NOT LIKE %s", $exclude_page_slug );
        }
    }

    return $search;
}
add_filter( 'posts_search', 'search_only_title_except_pages', 10, 2 );
?>
