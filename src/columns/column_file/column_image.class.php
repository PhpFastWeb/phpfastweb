<?php
class column_image extends column_file implements icolumn {
	
	
	private $limit_len;
	public function set_limit_len($max_len) {
		$this->limit_len = $max_len;
	}


	
	function get_representation( $dir, $file) {

//		$src = $this->get_src();
//		$t = $this->get_shortened_filename();
//		$result = '<img src="'.$src.'" alt="" />';
//		return $result;
		
		
		if ( substr($dir,-1,0)=='/'  ) {
			$dir = substr($dir,0,-1);
		}
		
		$result = '';
		$img = new image ();
		$img->filename = $file;
		//$img->dir = $dir;
		$img->generator_url = $this->generator_url; 
		$img->pass_dir_in_url = false;
		
		//$this->get_src()
		//$img->thumb_cache_dir = $this->image_thumb_cache_dir;
		$result .= $img->get_thumb_link ();
		
		$result .= $img->get_thumb_img ();
		//echo "<br clear=\"all\" />";
		$result .=  $this->get_shortened_filename();
		$result .=  "</a>";
		return $result;
	}
}

?>