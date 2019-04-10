<?php 
	function base64_to_jpeg($postArray,$uploadPath = '') {

		if ($uploadPath == '') {
			$uploadPath = 'uploads/';
		}

		if(!is_dir($uploadPath)){
			mkdir($uploadPath,0755);
		}
		if (count($postArray['images']) == count($postArray['images'], COUNT_RECURSIVE))
		{	
			if (trim($postArray['images']['base64']) != '') {	
				$img = explode(',', $postArray['images']['base64']);
				$ini =substr($img[0], 11);
				$type = explode(';', $ini);

				$output_file = $uploadPath.strtotime(date('Y-m-d H:i:s')).mt_rand(0,100).'.'.$type[0];
				// open the output file for writing
				$ifp = fopen( $output_file, 'wb' ); 
				chmod($output_file,0755);
				// split the string on commas
				// $data[ 0 ] == "data:image/png;base64"
				// $data[ 1 ] == <actual base64 string>
				$data = explode( ',', $postArray['images']['base64'] );

				// we could add validation here with ensuring count( $data ) > 1
				fwrite( $ifp, base64_decode( $data[ 1 ] ) );

				// clean up the file resource
				fclose( $ifp );
				$postArray['images']['imageUrl'] = $output_file;
				$postArray['images']['base64'] = '';
			}
			if ($postArray['imageUrl'] == '') {
				$postArray['images'] = [];
			}
		}
		else
		{
			foreach ($postArray['images'] as $key => $value) {
				if (trim($value['base64']) != '') {
					$img = explode(',', $value['base64']);
					$ini =substr($img[0], 11);
					$type = explode(';', $ini);

					$output_file = $uploadPath.strtotime(date('Y-m-d H:i:s')).mt_rand(0,100).'.'.$type[0];
					// open the output file for writing
					$ifp = fopen( $output_file, 'wb' ); 
					chmod($output_file,0755);
					// split the string on commas
					// $data[ 0 ] == "data:image/png;base64"
					// $data[ 1 ] == <actual base64 string>
					$data = explode( ',', $value['base64'] );

					// we could add validation here with ensuring count( $data ) > 1
					fwrite( $ifp, base64_decode( $data[ 1 ] ) );

					// clean up the file resource
					fclose( $ifp );
					$postArray['images'][$key]['imageUrl'] = $output_file;
					$postArray['images'][$key]['base64'] = '';
				}
			}
			foreach ($postArray['images'] as $key => $value) {
				if ($value['imageUrl'] == '') {
					unset($postArray['images'][$key]);
				}
			}
		}

	    return $postArray; 
	}

	$updatedPost = base64_to_jpeg($_POST,'uploads/');

	print_r($updatedPost);	
?>