<?php


//add_action('pre_get_posts', 'display_concerts');
//add_action('pre_get_posts', 'display_action');



function display_concerts($query){
	if($query->is_front_page() && $query->is_main_query())
	{
		$query->set('post_type',array('concert'));

		//10 dernières années
		$query->set('date_query', array('year' => getdate()['year']-10, 'compare'=> '>='));

		//entre 2006 et 2008
		/*$query->set('date_query', array('year' => '2006', 'compare'=> '>=',
			'year' => '2008', 'compare' => '<='));*/



		//le lieu n'est pas spécifié
		//$query->set('meta_query', array(array('key' => 'wpcf-lieu', 'value'=> 'false', 'type' => BOOLEAN)));

		//qui possède une image à la une
		//$query->set('meta_query', array(array('key' => '_thumbnail_id','compare' => 'EXISTS')));

return;
}

}


function display_action($query){
	if($query->is_front_page() && $query->is_main_query())
	{
		$query->set('post_type',array('action'));

		//10 dernières années
		$query->set('date_query', array('year' => getdate()['year']-10, 'compare'=> '>='));

		//entre 2006 et 2008
		/*$query->set('date_query', array('year' => '2006', 'compare'=> '>=',
			'year' => '2008', 'compare' => '<='));*/



		//le lieu n'est pas spécifié
		//$query->set('meta_query', array(array('key' => 'wpcf-lieu', 'value'=> 'false', 'type' => BOOLEAN)));

		//qui possède une image à la une
		//$query->set('meta_query', array(array('key' => '_thumbnail_id','compare' => 'EXISTS')));

return;
}

}

function dashboard_widget_function(){
	$query = new WP_Query();
	$query->set('post_type',array('concert'));
	$query->set('meta_query', array(array('key' => 'wpcf-lieu',
		'value'=> false,
		'type' => BOOLEAN)));
	echo "Il y a ".count($query->get_posts())." concerts pour lequelle le lieu n'est pas spécifié";


}

function add_dashboard_widgets(){
	wp_add_dashboard_widget('dashboard_widget', 'Lieu non spécifié', 'dashboard_widget_function' );
}

add_action('wp_dashboard_setup', 'add_dashboard_widgets');


function geolocalize($post_id) {
	if ( wp_is_post_revision( $post_id ) )
		return;
	$post = get_post($post_id);

	if ( !in_array( $post->post_type, array('concert') ) )
		return;
	$lieu = get_post_meta($post_id, 'wpcf-lieu', true);

	if(empty($lieu))
		return;
	$lat = get_post_meta($post_id, 'lat', true);

	if(empty($lat))
	{

		$address =  $lieu . ', France';
		$result = doGeolocation($address);

		if(false === $result)
			return;
		try{

			$location = $result[0]['geometry']['location'];
			add_post_meta($post_id, 'lat', $location["lat"]);
			add_post_meta($post_id, 'lng', $location["lng"]);

		}catch(Exception $e)
		{
			return;
		}

	}

}

add_action( 'save_post', 'geolocalize' );






function doGeolocation($address){

	$url = "http://maps.google.com/maps/api/geocode/json?sensor=false" . "&address=" . urlencode($address);


	$proxy='wwwcache.univ-orleans.fr:3128';
	$ctx = stream_context_create(array(
		'http' => array(
			'timeout' => 5,
			'proxy' => $proxy,
			'request_fulluri' => true,
			)
		)
	);

	if($json = file_get_contents($url,0,$ctx)){

		$data = json_decode($json, TRUE);

		if($data['status']=="OK"){

			return $data['results'];
		}

	}

	return false;

}


function load_scripts() {
	if(! is_post_type_archive('concert') && ! is_post_type_archive('action'))
		return;
	wp_register_script(
		'leaflet-js',
		'http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.js'
		)
	;
	wp_enqueue_script( 'leaflet-js' );
}

add_action('wp_enqueue_scripts', 'load_scripts');


function load_style() {
	if(! is_post_type_archive('concert') && ! is_post_type_archive('action'))
		return;
	wp_register_style(
		'leaflet-style',
		'http://cdn.leafletjs.com/leaflet-0.7.2/leaflet.css'
		)
	;
	wp_enqueue_style( 'leaflet-style' );
}

add_action('wp_enqueue_scripts', 'load_style');

function getPostWithLatLon($post_type = "concert")
{
	global $wpdb;
	$query = "
	SELECT
	ID, post_title, p1.meta_value as lat, p2.meta_value as lng
	FROM wp_archetsposts, wp_archetspostmeta as p1, wp_archetspostmeta as p2
	WHERE wp_archetsposts.post_type = 'concert'
	AND p1.post_id = wp_archetsposts.ID
	AND p2.post_id = wp_archetsposts.ID
	AND p1.meta_key = 'lat'
	AND p2.meta_key = 'lng'";

	return $wpdb->get_results($query, OBJECT);

}



function getMarkerList($post_type = "concert"){

    $results = getPostWithLatLon($post_type);
    $array = array();
    foreach($results as $result){
        $array[] = "var marker_".$result->ID." = L.marker([".$result->lat.", ".$result->lng."]).addTo(map);\n";
        $array[] = "var popup_$result->ID  = L.popup().setContent('".$result->post_title."');\n";
        $array[] = "popup_".$result->ID.".post_id = ".$result->ID.";\n";
        $array[] = "marker_".$result->ID.".bindPopup(popup_".$result->ID.");\n";
    }

    return implode(PHP_EOL, $array);
}



add_action("wp_ajax_popup_content","get_content");
add_action("wp_ajax_nopriv_popup_content","get_content");

function get_content(){
    if( !wp_verify_nonce($_REQUEST['nonce'], "popup_content")) {
        exit("d'où vient cette requête ?");
    }else{
        $post_id=$_REQUEST["post_id"];
        $post = get_post($post_id);
        $title = $post->post_title;
        $content = $post->post_content;
        print "<p>".$title."</p>".$content; // ! ceci sera renvoyé au client !
    }die(); //important
}




?>
