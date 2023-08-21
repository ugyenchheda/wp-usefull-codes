//function
<?php 
add_action('wp_ajax_loadingNews', 'loadingNews');
add_action('wp_ajax_nopriv_loadingNews', 'loadingNews');
function loadingNews() {
    $homepage_news_category = $_POST['homepage_news_category'];
    $no_of_news_hp = $_POST['no_of_news_hp'];
    $page = $_POST['page'];
    $loaded_post_ids = isset($_POST['loaded_post_ids']) ? $_POST['loaded_post_ids'] : array();

    $args = array(
        'posts_per_page' => $no_of_news_hp,
        'post_type'      => 'news',
        'orderby' => 'date',
        'order' => 'DESC',
        'post__not_in' => $loaded_post_ids,
		'offset' => 2,
    );

    $query = new WP_Query($args);

    ob_start(); 

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $loaded_post_ids[] = get_the_ID();
            echo '<div class="col-lg-4 col-md-4">
                <div class="trending-news-item mb-30">
                    <div class="trending-news-thumb">' . get_the_post_thumbnail($post->ID, 'post_image_l') . '</div>
                    <div class="trending-news-content">
                        <div class="post-meta">';

							$taxonomies = get_object_taxonomies('news'); // Replace 'post' with your desired post type

							foreach ($taxonomies as $taxonomy) {
								if (!in_array($taxonomy, ['category', 'post_tag'])) {
									$terms = get_the_terms(get_the_ID(), $taxonomy);
									if ($terms && !is_wp_error($terms)) {
										echo '<div class="meta-categories">';
										$term = reset($terms);
											echo '<a href="' . esc_url(get_term_link($term)) . '" class="home-event">' . esc_html($term->name) . '</a> ';
										echo '</div>';
									}
								}
							}

							echo '<div class="meta-date">
									<span>' . get_the_date('F j, Y') . '</span>
								</div>
							</div>
							<h3 class="title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>
							<p class="text">' . wp_trim_words(get_the_excerpt(), 20) . '</p>
						</div>
					</div>
				</div>';
			}
			wp_reset_postdata();
		}
	
		$response = ob_get_clean(); // Get the buffered output and store it in $response variable
		$max_pages = $query->max_num_pages;
		$last_page = $query->max_num_pages === $page;
		wp_send_json(array('content' => $response, 'max_pages' => $max_pages));
	}

?>
//javascript code
<script>
jQuery(document).ready(function() {
    var currentPage = 1;
    var maxPages = 1; // Initialize maxPages to 1
    var loading = false;
	jQuery('#fully-loaded').hide();
    jQuery(document).on('click', '#load-more-news', function() {
        if (!loading && currentPage <= maxPages) { // Modify the condition to include equal to
            loading = true; // Set loading to true to prevent multiple AJAX requests
            var nextPage = currentPage + 1;
            var ajaxurl = my_ajax_object.ajax_url;
            var loadedPostIds = []; // Array to store the loaded post IDs

            // Get the container element where the news items will be appended
            var $appendContainer = jQuery('#append-here');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'loadingNews',
                    page: nextPage,
                    homepage_news_category: my_ajax_object.homepage_news_category,
                    no_of_news_hp: my_ajax_object.no_of_news_hp,
                    loaded_post_ids: loadedPostIds,
                },
                beforeSend: function() {
                    $('#load-more-news').html('<span>Loading <i class="fa fa-spinner fa-spin"></i></span>');
                },
                success: function(response) {
                    $('#load-more-news').text('Load More'); // Reset the button text
                    if (response.content) {
                        $appendContainer.append(response.content); // Append the new news items to the container
                        currentPage = nextPage; // Update the current page
                        loading = false; // Reset loading flag after success
                        loadedPostIds = response.loaded_post_ids; // Update the loaded post IDs

                        // Check if it's the last page and hide the button if true
						if (currentPage >= response.max_pages) {
                            jQuery('#load-more-news').hide();
                            jQuery('#fully-loaded').show(); // Show the fully-loaded element
                        } else {
                            jQuery('#load-more-news').show(); // Show the load more button if there are more pages
                            jQuery('#fully-loaded').hide(); // Hide the fully-loaded element if there are more pages
                        }
                        // Update maxPages with the actual value from the server response
                        maxPages = response.max_pages;
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    loading = false; // Reset loading to false in case of an error
                }
            });
        }
    });
		       </script>
//html
    <section class="all-post-area">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-12">
                    <div class="post-entertainment">
                        <div class="section-title">
					        <?php $homepage_news_title = get_theme_mod('hompage_news_title');?>
                            <h3 class="title"><?php echo $homepage_news_title; ?></h3>
                        </div>
                        <div class="row" id="append-here">
                            <?php
                                $homepage_news_category = get_theme_mod('homepage_news_category');
                                $no_of_news_hp = get_theme_mod('no_of_news_hp');
                                $loaded_post_ids = isset($_POST['loaded_post_ids']) ? $_POST['loaded_post_ids'] : array();

                                // Check if $homepage_news is an array and not empty
                                if (!empty($homepage_news_category)) {

                                    $args = array(
                                        'posts_per_page' => $no_of_news_hp,
                                        'post_type'      => 'news',// Display 3 latest news posts
                                        'orderby' => 'date', // Order by the latest date
                                        'order' => 'DESC', 
                                        'post__not_in' => $loaded_post_ids,
                                    );

                                    $query = new WP_Query($args);

                                    if ($query->have_posts()) {
                                        while ($query->have_posts()) {
                                            $query->the_post();
                                            $loaded_post_ids[] = get_the_ID();
                                            echo '<div class="col-lg-4 col-md-4">
                                                    <div class="trending-news-item mb-30">
                                                        <div class="trending-news-thumb">
                                                        ' . get_the_post_thumbnail($post->ID, 'post_image_l') . '
                                                            <div class="circle-bar">
                                                                <div class="first circle">
                                                                    <strong></strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="trending-news-content">
                                                            <div class="post-meta">';
                                                                $taxonomies = get_object_taxonomies('news'); // Replace 'post' with your desired post type
                                                                foreach ($taxonomies as $taxonomy) {
                                                                    if (!in_array($taxonomy, ['category', 'post_tag'])) {
                                                                        $terms = get_the_terms(get_the_ID(), $taxonomy);
                                                                        if ($terms && !is_wp_error($terms)) {
                                                                            echo '<div class="meta-categories">';
                                                                            $term = reset($terms);
                                                                                echo '<a href="' . esc_url(get_term_link($term)) . '" class="home-event">' . esc_html($term->name) . '</a> ';
                                                                            echo '</div>';
                                                                        }
                                                                    }
                                                                }
                                                                echo '<div class="meta-date"><span>' . get_the_date('F j, Y') . '</span></div>
                                                            </div>
                                                            <h3 class="title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>
                                                            <p class="text">' . wp_trim_words(get_the_excerpt(), 20) . '</p>
                                                        </div>
                                                    </div>
                                                </div>';
                                        }
                                    } else {
                                        echo '<div class="trending-item"><p>No news available currently.</p></div>';
                                    }
                                }
                                wp_reset_postdata(); // Restore original post data
                            ?>
                        </div>
                            <div class="load-more-container">
                                <button id="load-more-news">View More News</button>
                                <p id="fully-loaded">Hooray! You caught up with all the news for today.</p>
                            </div>
                            
                    </div>
                </div>
            </div>
            <?php 
                $footer_adv_link = get_theme_mod('footer_adv_link');
                $adv_banner_footer = get_theme_mod('adv_banner_footer', get_template_directory_uri() . '/assets/images/ad/ad-1.png');
                if(!empty($adv_banner_footer)) {?>
                    <div class="sidebar-add mt-30">
                        <a href="<?php echo $footer_adv_link; ?>"><img src="<?php echo $adv_banner_footer ; ?>"  class="img-responsive"></a>
                    </div>
            <?php }; ?>
        </div>
    </section>

// another example:
// add this code to function.php
<?php
add_action('wp_ajax_loadingNews', 'loadingNews');
add_action('wp_ajax_nopriv_loadingNews', 'loadingNews');
function loadingNews() {
    $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
    $loaded_post_ids = isset($_POST['loaded_post_ids']) ? $_POST['loaded_post_ids'] : array();

    $args = array(
        'posts_per_page' => 1,
        'post_type'      => 'post',
        'orderby' => 'date',
        'order' => 'DESC',
        'post__not_in' => $loaded_post_ids,
		'offset' => 2,
    );

    $query = new WP_Query($args);

    ob_start();

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $loaded_post_ids[] = get_the_ID();
            echo '<div class="col-md-6">
                    <a href="' . get_the_permalink() . '" class="blog-box">
                      <div class="blog-image">
                      ' . get_the_post_thumbnail(get_the_ID(), 'post_image_xl', array('class' => 'alignleft')) . '
                        <div class="blog-icon">
                          <i class="bi bi-journal-text"></i>
                        </div>
                      </div>
                      <div class="blog-post-content">
                        <h6 class="blog-header">' . get_the_title() . '</h6>
                        <div class="blog-dates">
                          <span>' . get_the_date() . '</span>
                        </div>
                        <p class="mb-0">' . get_the_excerpt() . '</p>
                      </div>
                    </a>
                  </div>';
        }
        wp_reset_postdata();
    }

    $response = ob_get_clean();
    $max_pages = $query->max_num_pages;

    wp_send_json(array('content' => $response, 'max_pages' => $max_pages));
}
?>
//add this code to your js file.
<script>

jQuery(document).ready(function() {
    var currentPage = 1;
    var maxPages = 1; // Initialize maxPages to 1
    var loading = false;
    var noMorePosts = false; // Flag to track if there are no more posts

    jQuery('#fully-loaded').hide();

    jQuery(document).on('click', '#load-more-news', function() {
        if (!loading && !noMorePosts && currentPage <= maxPages) {
            loading = true;
            var nextPage = currentPage + 1;
            var ajaxurl = my_ajax_object.ajax_url;
            var loadedPostIds = [];

            var $appendContainer = jQuery('#append-here');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'loadingNews',
                    page: nextPage,
                    no_of_news_hp: my_ajax_object.no_of_news_hp,
                    loaded_post_ids: loadedPostIds,
                },
                beforeSend: function() {
                    $('#load-more-news').html('<span>Loading <i class="fa fa-spinner fa-spin"></i></span>');
                },
                success: function(response) {
                    $('#load-more-news').text('Load More');
                    if (response.content) {
                        $appendContainer.append(response.content);
                        currentPage = nextPage;
                        loading = false;
                        loadedPostIds = response.loaded_post_ids;

                        if (currentPage >= response.max_pages) {
                            noMorePosts = true; // No more posts to load
                            jQuery('#load-more-news').hide();
                            jQuery('#fully-loaded').show();
                        }

                        maxPages = response.max_pages;
                    } else {
                        noMorePosts = true; // No more posts to load
                        jQuery('#load-more-news').hide();
                        jQuery('#fully-loaded').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.log(xhr.responseText);
                    loading = false;
                }
            });
        }
    });
});
</script>


