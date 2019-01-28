<?php
/**
 * 
 */
class Parser
{
	
	public function __construct($fileLots)
	{
		# ім'я файлу з лотами
        $this->fileLots = $fileLots;
	}

	public function run_Parser() {
		//$this->getTax();
		//exit;
		include 'phpQuery-onefile.php';
		$url = 'https://abetter.bid/ru/car-finder/type-automobiles';
		//echo "stream file:".$this->fileLots.'<br>';
		$lines = file($this->fileLots);
		foreach ($lines as $line_num => $lot) {
			//echo "Оброблено лот:".$lot."<br>";
			$start = 0;
			$isL = $this->parser($url, $lot, $start);
			echo "<h2>лот:".$lot;
			echo ($isL) ? " оброблено" : " торги завершено.Лот не записано";
			echo "</h2>";
		}
	}

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

	private function parser($url, $lot, $start) {
		$page   = $this->getCurl($url);
		$doc    = phpQuery::newDocument($page);
		$hentry = $doc->find('.carlist');
		$i = 0;
		$isLot  = false;
		foreach ($hentry as $el) {
			$elem_pq = pq($el); 
			$trs = $elem_pq->find('tr');
			$i++;
			//echo "i=".$i."<br>";
			$hr   = $elem_pq->find('a');
			$href = $hr->attr('href');
			if(strpos($href, trim($lot))) {

				$propss = $this->getUrl($href);
				foreach ($propss as $value) {
					//echo $value['name'].': '.$value['val'] . "<br>";
				}
				//getPhoto($href);
				if ($this->saveLot($propss)) {
					//echo "тут є лот i=".$i."<br>href:".$href.'<br>';
					//echo "оброблено лот:".$lot.'<br>';
					$isLot = true;
					break;					
				}
				else {
					echo "somethihg wrong";
				}

			}
		}
		if (!$isLot) {		 
			$nextpage = $doc->find(".pagination .active")->next()->find('a')->attr("href");
			if (!empty($nextpage)) {
				$next = $nextpage;
				$start++;				
				if ($start < 3) {
					$this->parser($next, $lot, $start);	
				}				
			}
		}
		return $isLot;
	}

	private function getUrl($href) {
		$properties=[];
		$url ="https://abetter.bid".$href;
		$doc = $this->getCurl($url);

		$details = $doc->find('.detailstable');
		$d =0;
		//echo "d=$d<br>";
		$d++;
		foreach ($details as $det) {
			if ($d == 3) {
				$elem_pq = pq($det); 
				$trs = $elem_pq->find('tr');
				$i++;		
				foreach ($trs as $tr) {
					if ($i == 1) {
						$cont = pq($tr);
						$name = $cont->find("td:eq(0)")->text();
						$val  = $cont->find("td:eq(1)")->text();
//echo "3 entry  name:$name    val=$val<br>";
						$new_item = [
							'name' => trim($name),
							'val'  => trim($val),
						];
						array_push($properties, $new_item);
					}
				}				
			}
			//echo "d=$d<br>";
			$d++;
		}

		$idYear = $doc->find('#lot-title')->text();
		//echo "year:$idYear<br>";
		$year = intval(explode(" ",$idYear)[1]);
		//echo "year:$year<br>";
		$new_item = [
			'name' => trim('sl_year'),
			'val'  => trim($year),
		];
		array_push($properties, $new_item);
		$hentry = $doc->find('.vehicle-properties');
		if ($hentry) {
			//echo "is hentry";
			$i = 0;
			
			foreach ($hentry as $el) {
				$elem_pq = pq($el); 
				$trs = $elem_pq->find('tr');
				$i++;		
				foreach ($trs as $tr) {
					if ($i == 1) {
						$cont = pq($tr);
						$name = $cont->find("td:eq(0)")->text();
						$val  = $cont->find("td:eq(1)")->text();
						$new_item = [
							'name' => trim($name),
							'val'  => trim($val),
						];
						array_push($properties, $new_item);
					}
				}
			}
			return $properties;
		}
		else {
			echo "no hentry";
		}	
	}

	private function getCurl($url) {
		$curl = curl_init($url);

		curl_setopt ($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt ($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt ($curl, CURLOPT_FOLLOWLOCATION, 1); //следование 302 redirect 

		$page = curl_exec($curl);
		$doc = phpQuery::newDocument($page);
		return $doc;
	}

	private function saveLot($pars) {
	$metaInput = [];
		foreach ($pars as $pr) {
			if ($pr['name'] == 'Тип документа') {
				$new_item = [
					'd0b4d0bed0bad183d0bcd0b5d0bdd182' => $pr['val'],
				];
				array_push($metaInput, $new_item);
				$type_doc = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'sl_year') {
				$new_item = [
					'registration' => $pr['val'],
				];
				array_push($metaInput, $new_item);			
				$sl_year = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Место Аукциона') {
				$l = trim(explode("Показать",$pr['val'])[0]);
				$new_item = [
					'd0bfd180d0bed0b1d0b5d0b3-d0bcd0b8d0bbd18c' => $l,
				];
				array_push($metaInput, $new_item);			
				$location = $l;
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Sale Type') {
				$new_item = [
					'd182d0b8d0bf-d0bfd180d0bed0b4d0b0d0b6d0b8' => $pr['val'],
				];
				array_push($metaInput, $new_item);			
				$sale_Type = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Начальная ставка') {
				$start_bid = intval(explode("$",$pr['val'])[1]);
				$new_item = [
					'd182d0b5d183d0bad183d189d0b0d18f-d181d182d0b0d0b2d0bad0b0' => $start_bid,
				];
				array_push($metaInput, $new_item);			
				//$start_bid = floatval($pr['val']);
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Одометр') {
				$new_item = [
					'd0bfd180d0bed0b1d0b5d0b3-d0bcd0b8d0bbd18c' => $pr['val'],
				];
				array_push($metaInput, $new_item);			
				$odom = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Transmission') {
				$new_item = [
					'd182d180d0b0d0bdd181d0bcd0b8d181d181d0b8d18f'=>$pr['val'], //Трансмиссия
				];
				array_push($metaInput, $new_item);			
				$trans = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Первичное повреждение') {
				$new_item = [
					'd0bed181d0bdd0bed0b2d0bdd0bed0b5-d0bfd0bed0b2d180d0b5d0b6d0b4d0b5d0bdd0b8d0b5'=>$pr['val'], //Основное повреждение
				];
				array_push($metaInput, $new_item);			
				$primary_damage = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Вторичное повреждение') {
				$new_item = [
					'd0b4d0bed0bfd0bed0bbd0bdd0b8d182d0b5d0bbd18cd0bdd0bed0b5-d0bfd0bed0b2d180d0b5d0b6d0b4d0b5d0bdd0b8d0b5'=>$pr['val'], //Основное повреждение
				];
				array_push($metaInput, $new_item);			
				$next_damage = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'VIN') {
				$new_item = [
					'vin'=>$pr['val'], //
				];
				array_push($metaInput, $new_item);
				$vin = explode(" ",$pr['val'])[0];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Цвет') {
				$new_item = [
					'd186d0b2d0b5d182'=>$pr['val'], //
				];
				array_push($metaInput, $new_item);			
				$colour = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Тип кузова') {
				$new_item = [
					'd182d0b8d0bf-d0bad183d0b7d0bed0b2d0b0'=>$pr['val'], //
				];
				array_push($metaInput, $new_item);			
				$body_type = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Тип двигателя') {
				$new_item = [
					'd182d0b8d0bf-d0b4d0b2d0b8d0b3d0b0d182d0b5d0bbd18f'=>$pr['val'], //
				];
				array_push($metaInput, $new_item);			
				$engine_type = trim($pr['val']);
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Управление') {
				$new_item = [
					'd0bfd180d0b8d0b2d0bed0b4'=>$pr['val'], //
				];
				array_push($metaInput, $new_item);			
				$privod = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Цилиндр') {
				$new_item = [
					'd186d0b8d0bbd0b8d0bdd0b4d180d0bed0b2'=>$pr['val'], //
				];
				array_push($metaInput, $new_item);			
				$cyl = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Топливо') {
				$new_item = [
					'd182d0bed0bfd0bbd0b8d0b2d0be'=>$pr['val'], //
				];
				array_push($metaInput, $new_item);			
				$gas = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Лот #') {
				$new_item = [
					'd0bbd0bed182-d0bdd0bed0bcd0b5d180'=>$pr['val'], //
				];
				array_push($metaInput, $new_item);			
				$NomLot = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Ключи') {
				$new_item = [
					'd0bad0bbd18ed187d0b8'=>$pr['val'], //
				];
				array_push($metaInput, $new_item);			
				$keys = $pr['val'];
				//echo '<br>111'.$pr['name'].":".$pr['val'].'  key:'.key($pr).'<br>';
			}
			elseif ($pr['name'] == 'Марка') {
				$make = $this->saveTax('make', $pr['val']);
				$mark = $pr['val'];
			}
			elseif ($pr['name'] == 'Модель') {
				$mod = $this->saveTax('model', $pr['val']);
				$model = $pr['val'];
			}
			//echo $pr['name'].":".$pr['val'].'<br>';	
		}
		//print_r($metaInput);
//echo '<pre>'; //html тег для более наглядного вывода ( вместо \n в каждой строке)
//print_r(get_object_vars($metaInput));// print_r выводит массив		
		$post_data = array(
			'post_type'     => 'vehicle',
			'post_title'    => $model.' '.$mark,
			'post_content'  => 'post_content',
			'post_status'   => 'publish',
			'post_author'   => 1,
			'tax_input'     => array( 'vehicle_type' => array( $body_type ), 'model' => array( $model ), 'make' => array( $mark )  ),
//			'meta_input'    => array($metaInput),
			'meta_input'    => array(
				'd0b4d0bed0bad183d0bcd0b5d0bdd182' => $type_doc,
				'd182d180d0b0d0bdd181d0bcd0b8d181d181d0b8d18f'=>$trans,
				'd0bed181d0bdd0bed0b2d0bdd0bed0b5-d0bfd0bed0b2d180d0b5d0b6d0b4d0b5d0bdd0b8d0b5'=>$primary_damage,
				'd0b4d0bed0bfd0bed0bbd0bdd0b8d182d0b5d0bbd18cd0bdd0bed0b5-d0bfd0bed0b2d180d0b5d0b6d0b4d0b5d0bdd0b8d0b5'=>$next_damage,				
				'd182d0b8d0bf-d0bad183d0b7d0bed0b2d0b0'=>$body_type,
				'd186d0b2d0b5d182'=>$colour,
				'd0bfd180d0b8d0b2d0bed0b4'=>$privod,
				'd186d0b8d0bbd0b8d0bdd0b4d180d0bed0b2'=>$cyl,
				'd182d0bed0bfd0bbd0b8d0b2d0be'=>$gas,
				'vin'=>$vin,
				'registration'=>$sl_year,
				'milage'=>$odom,
				'd0b4d0b2d0b8d0b3d0b0d182d0b5d0bbd18c'=>$engine_type,
				'd0bed181d0bd-d0bfd0bed0b2d180d0b5d0b6'=>$primary_damage,
				'd0bbd0bed182-d0bdd0bed0bcd0b5d180'=>$NomLot,
				'd0bad0bbd18ed187d0b8'=>$keys,
				'd180d0b0d181d0bfd0bed0bbd0bed0b6d0b5d0bdd0b8d0b5'=>$location,
				'd182d0b8d0bf-d0bfd180d0bed0b4d0b0d0b6d0b8'=>$sale_Type,
				'd182d0b5d183d0bad183d189d0b0d18f-d181d182d0b0d0b2d0bad0b0'=>$start_bid,
				'pricetext'=>$start_bid,
			),
			'post_category' => array(8,39)
		);

		// Вставляем данные в БД
		return wp_insert_post( wp_slash($post_data, $wp_error=true) );
	}


	private function saveTax($taxonomy, $val) {
		$term = term_exists( $val, $taxonomy ); 
		if ($term) {
			return  $term['term_taxonomy_id'];		
		}
		$tax = wp_insert_term($val,$taxonomy,  array(
			'description' => '',
			'parent'      => 0,
			'slug'        => $val,
		) );
		$term = term_exists( $val, $taxonomy );
		print_r($term); 
		if ($term) {
				
			// ID элемента таксономии
			//echo $term['term_id'];

			// ID элемента таксономии в структуре таксономий
			//echo $term['term_taxonomy_id'];
			return  $term['term_taxonomy_id'];		
		}		
		
	}
}
?>