 <?php
 $isUpLoad = true;
if( wp_verify_nonce( $_POST['fileup_nonce'], 'my_file_upload' ) ) {
	if ( ! function_exists( 'wp_handle_upload' ) ) 
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
	$file      = & $_FILES['my_file_upload'];
	$overrides = [ 'test_form' => false ];
	$movefile  = wp_handle_upload( $file, $overrides );
	if ( $movefile && empty($movefile['error']) ) {
		require plugin_dir_path( __FILE__ ) . 'class-parser.php';
		$parser = new Parser($movefile['url']);
		$parser->run_Parser();
	} else {
		echo "Виберіть тип файлу txt\n";
	}
	$isUpLoad = false;
}
?>
<?php if ($isUpLoad) :?>
<h1>Завантаження лотів</h1>
<form enctype="multipart/form-data" action="" method="POST">
	<?php wp_nonce_field( 'my_file_upload', 'fileup_nonce' ); ?>
	<input name="my_file_upload" type="file" />
	<!-- <input type="submit" value="Загрузить файл" /> -->
	<button name="submit" type="submit" id="iaaiclick">Завантажити</button>
</form>
<!-- <div class='loaderDiv'><img src="<? echo plugin_dir_path( __FILE__ )?>image/loading.gif"></div>
<div class='loaderDiv'>image/loading.gif</div> -->
<?php else : ?>
<h1>Завантаження завершено</h1>	
<?php endif; ?>