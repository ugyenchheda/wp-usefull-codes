<?php 
// code to be added in customizer.php

	$wp_customize->add_panel('nepaleseinfinland_setting_panel', array(
				'capability' 		=> 'edit_theme_options',
				'theme_supports' 	=> '',
				'title' 			=> __('Nepalese In Finland Settings', 'nepaleseinfinland'),
				'description' 		=> __('Setup website Settings', 'nepaleseinfinland'),
				'priority' 			=> 12,
		));


	$wp_customize->add_section(
		'section_headersetting' ,
		array(
			'title'       	=> __( 'Header Setting', 'nepaleseinfinland' ),
			'description' 	=> __( 'Setup header settings.', 'nepaleseinfinland' ),
			'panel'			=> 'nepaleseinfinland_setting_panel',
			'priority' => 29,
		)
	);

	$wp_customize->add_setting(
		'news_title',
		array(
			'default'			=> 'Trending',
		)
   );
	$wp_customize->add_control(
		'news_title',
			array(
			 'label'		=> __('Title for News', 'nepaleseinfinland'),
			 'section' 	=> 'section_headersetting',
			 'type' 		=> 'text',
			 'settings'	=> 'news_title',
			)
	);
	
	$terms = get_terms(array(
		'taxonomy'=> 'news_category',
		'hide_empty'=> false,
	));
	$cats = array();
	$i = 0;
	foreach($terms as $category){
		$cats[$category->term_id] = $category->name;
	}
	
	$wp_customize->add_setting('news_highlight', 
		array(
			
			)
	);
		
	$wp_customize->add_control(
		'news_highlight',
			array(
			'label'		=> __('Choose Category:', 'nepaleseinfinland'),
			'description' => 'Select news category to display in slider on top bar.',
			'section' 	=> 'section_headersetting',
			'type' 		=> 'text',
			'settings'	=> 'news_highlight',
			'type'    => 'select',
			'choices' => $cats
			)
	);

	$wp_customize->add_setting(
		'news_number',
		array(
			'default'			=> '5',
		)
   );
	$wp_customize->add_control(
		'news_number',
			array(
			 'label'		=> __('Select total news to display on the top bar:', 'nepaleseinfinland'),
			 'section' 	=> 'section_headersetting',
			 'type' 		=> 'text',
			 'settings'	=> 'news_number',
			)
	);
?>
//call the parameters on frontend

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
// add your own html
