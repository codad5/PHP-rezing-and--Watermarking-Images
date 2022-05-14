<?php

function prepare_file_array(Array $files){
	$new_files_array = [];
	$position_counter = 0;
    for ($i=0; $i < count($files['name']); $i++) { 
    	# code...
    	foreach ($files as $key => $value) {
    		# code...
    		$new_files_array[$i][$key] = $value[$i];
    	}
		$new_files_array[$i]['extension']  = explode('.', $files['name'][$i]);
		$new_files_array[$i]['extension'] = end($new_files_array[$i]['extension']);

    }
    return $new_files_array;
}