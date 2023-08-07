<?php //function to fetch lat lng from google map
function event_location($latitude,$longitude) {
	//Google Map API URL
	$API_KEY = get_theme_mod('google_map_api'); // Google Map Free API Key
	$url = "https://maps.google.com/maps/api/geocode/json?latlng=".$latitude.",".$longitude."&key=".$API_KEY."";
	// Send CURL Request
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	$response = curl_exec($ch);
	curl_close($ch);
	$returnBody = json_decode($response);
	// Google MAP
	$status = $returnBody->status;
	if($longitude||$latitude){
		if($status == "REQUEST_DENIED"){ 
			$result = $returnBody->error_message;
		} else { 
			$result = $returnBody->results[0]->formatted_address;
		}
		return $result;
	}
}

//postmeta get map

   $API_KEY = get_theme_mod('google_map_api'); 
    $cmb->add_field( array(
      'name' => 'UAS Location',
      'desc' => 'Drag the marker to set the exact location',
      'id' => 'uas_location',
      'type' => 'pw_map',
      'split_values' => true, 
      'api_key' => $API_KEY, 
    ) )
?>
//frontend for map rendering
                    <div id="map" style="height: 502px;" class="kindergarden_map"></div>
	                <?php $API_KEY = get_theme_mod('google_map_api'); ?>
                    <script async defer  src="https://maps.googleapis.com/maps/api/js?key=<?php echo $API_KEY;?>&callback=initMap">   </script>
                    <script>
                        function initMap() {
                            var uluru = {lat: <?php echo $uas_location['latitude'] ?>, lng: <?php echo $uas_location['longitude'] ?>};
                            var map = new google.maps.Map( document.getElementById("map"), {zoom: 13, center: uluru});
                            var marker = new google.maps.Marker({position: uluru, map: map});
                        }   
                    </script>
