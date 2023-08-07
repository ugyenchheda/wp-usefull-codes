<?php //function to get youtube id from youtube url
	function get_youtube_video_id($url) {
		$query_string = parse_url($url, PHP_URL_QUERY);
		parse_str($query_string, $query_params);
		if (isset($query_params['v'])) {
			return $query_params['v'];
		} else {
			return false;
		}
	}

//frontend data

$video_link = get_post_meta( get_the_ID(), 'video_link', true );
$get_video_id = get_youtube_video_id($video_link);
$final_video = 'https://www.youtube.com/watch?v='. $get_video_id;

?>
