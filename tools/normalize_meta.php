<?php
/*if ( is_user_logged_in() && WPUsers::isSiteEditor() ) {
	$pathToJson = __DIR__ . "/floormeta.json";
	$jsonData   = json_decode( file_get_contents( $pathToJson ), true );
	var_dump( count( $jsonData ) );
	$counter = 0;
	foreach ( $jsonData as $item ) {
		$postId = $item['post_id'];
		preg_match_all( '!\d+!', $item['meta_value'], $matches );
		$postType = wp_get_post_terms( $postId, TAX_PROPERTY_TYPE, [ 'fields' => 'slugs' ] );

		if ( isset( $postType[0] ) ) {
			$postType = $postType[0];
		} else {
			var_dump( $item );
		}

		$metas = $matches[0];
		if ( $postType == 'apartment' || $postType == 'comercial' ) {
			//If is just one element it is Floor Number
			$floorNumber = "";
			if ( isset( $metas[0] ) ) {
				$floorNumber = $metas[0];
				update_post_meta( $postId, MetaProperty::FLOOR, $floorNumber );
			}
			$floorsNumber = "";
			if ( isset( $metas[1] ) ) {
				$floorsNumber = $metas[1];
				update_post_meta( $postId, MetaProperty::FLOORS, $floorsNumber );
			}
			$counter ++;
		} else {
			$floorNumber = "";
			if ( count( $metas ) == 1 ) {
				$floorsNumber = $metas[0];
				update_post_meta( $postId, MetaProperty::FLOORS, $floorsNumber );
			} else {
				$floorNumber  = $metas[0];
				$floorsNumber = $metas[1];
				update_post_meta( $postId, MetaProperty::FLOOR, $floorNumber );
				update_post_meta( $postId, MetaProperty::FLOORS, $floorsNumber );
			}
			echo "$postType is on $floorNumber from $floorsNumber<br>";
			$counter ++;
		}
	}
	//echo delete_post_meta_by_key( 'REAL_HOMES_property_bathrooms' );
	var_dump( $counter );
}*/