<?php
function getTax() {
	global $car_dealer;
echo '<pre>'; //html тег для более наглядного вывода ( вместо \n в каждой строке)
print_r(get_object_vars($car_dealer));// print_r выводит массив

//	print_r($car_dealer);

	exit;
	$id = 3483;
$custom_fields = get_post_custom($id);
$my_custom_field = $custom_fields['_costOfRepairs'];
print_r($my_custom_field);
foreach ( $my_custom_field as $key => $value )
	echo $key . " => " . $value . "<br />";

$custom_fields = get_post_custom($id);
//$my_custom_field = $custom_fields['my_custom_field'];
foreach ( $custom_fields as $key => $value ) {
	echo "key:".$key."<br>";
	foreach ($value as $key1 => $val) {
		echo $key1 . " => " . $val . "<br />===========================<br />";
	}
	
}
//exit;
$custom_field_keys = get_post_custom_keys($id);
$post_id_7 = get_post( $id );
//$title = $post_id_7->post_title;
echo "title ".$post_id_7->post_title.'<br>';
foreach ( $custom_field_keys as $key => $value ) {
	$valuet = trim($value);
		if ( '_' == $valuet{0} )
			continue;

	echo $key .' => '. $value . '<br />';
}	
	$taxonomies = get_taxonomies();
	//wp_list_categories('taxonomy=vehicle_type');
	$post_meta = get_metadata('post', 3474 );
		foreach( $post_meta as $pp ) {
		echo '<p>'. $pp[0].' ;'.$pp[1]. '</p>';
	}
	print_r($post_meta);
	echo '<p>'. $post_meta. '</p>';
	foreach( $taxonomies as $taxonomy ) {
		echo '<p>'. $taxonomy. '</p>';
	}

	$terms = get_terms( array(
		'hide_empty'  => 0,  
		'orderby'     => 'name',
		'order'       => 'ASC',
		'taxonomy'    => 'make'
	) );
	// оставим только термины с parent=0

	echo '<p>taxonomy:</p>';
	//$terms = wp_list_filter( $terms );			
	//print_r($terms);			
	foreach( $terms as $term ) {
		
		echo '<br>name:'.$term->name."      term_taxonomy_id:".$term->term_taxonomy_id."      parent:".$term->parent."      term_group:".$term->term_group.'<br>';
	}
}