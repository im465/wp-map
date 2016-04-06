<!DOCTYPE html>
<html>
	<head>
		<title>Coyote Walk 2016</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>">
		<link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/lib/leaflet/dist/leaflet.css">
	</head>
	<body>
		<div id="map"></div>
		<script src="<?php echo get_template_directory_uri(); ?>/lib/jquery/dist/jquery.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/lib/leaflet/dist/leaflet.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/lib/leaflet-omnivore/leaflet-omnivore.min.js"></script>
		<script src="<?php echo get_template_directory_uri(); ?>/lib/tangram/dist/tangram.min.js"></script>
                <script>

                var base_url = '<?php echo get_template_directory_uri(); ?>';
                </script>
		<script src="<?php echo get_template_directory_uri(); ?>/coyote-map.js"></script>
                <script>
                <?php

while (have_posts()) {

  the_post();

  $id = get_the_ID();
  $url = get_permalink();
  $title = get_the_title();
  
  $lat = get_post_meta($id, 'lat', true);
  $lng = get_post_meta($id, 'lng', true);
  
  if ($lat && $lng) {
    echo "var lat = $lat;\n";
    echo "var lng = $lng;\n";
    echo "L.marker([lat, lng]).addTo(map);\n";
  }
  
}

?>
                </script>
	</body>
</html>
