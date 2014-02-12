<div id="wrap_carte">
<h5 id="titre_carte" ><u>Carte des concerts</u></h5>
<div id="map" ></div>
</div>
<script type="text/javascript">
	var map = L.map('map').setView([47.9, 1.9], 5);

	L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    	attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
	}).addTo(map);


	map.on('popupopen',function(e){
    var post_id = e.popup.post_id;
    var nonce = '<?php print wp_create_nonce("popup_content"); ?>';
    jQuery.post(
        "<?php print admin_url('admin-ajax.php')?>",
        {action:'popup_content', post_id:post_id,nonce:nonce},
        function(response){
            console.log("resp",response);
            e.popup.setContent(response);
        }
    );
});


</script>

<?php 

//echo getMarkerList();

echo "<script>".getMarkerList()."</script>"; 

?>