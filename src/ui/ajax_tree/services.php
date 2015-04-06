<?php
class AjaxTreeview {	
	public function getCatList($catid) {
		$arr1 = array(
			0 => array( 'id' => 1, 'name' => 'Hardware', 'pid' => 0),
			1 => array( 'id' => 2, 'name' => 'Software', 'pid' => 0),
			2 => array( 'id' => 3, 'name' => 'Movies', 'pid' => 0),
			3 => array( 'id' => 4, 'name' => 'Mouse', 'pid' => 1),
			4 => array( 'id' => 5, 'name' => 'Keyboard', 'pid' => 1),
			5 => array( 'id' => 6, 'name' => 'Monitor', 'pid' => 1),
			6 => array( 'id' => 7, 'name' => 'Desktop', 'pid' => 2),
			7 => array( 'id' => 8, 'name' => 'Web Application', 'pid' => 2),
			8 => array( 'id' => 9, 'name' => 'Mobile Application', 'pid' => 2),
			9 => array( 'id' => 10, 'name' => 'Hindi', 'pid' => 3),
			10 => array( 'id' => 11, 'name' => 'English', 'pid' => 3),
			11 => array( 'id' => 12, 'name' => 'Punjabi', 'pid' => 3),
			12 => array( 'id' => 13, 'name' => 'French', 'pid' => 3),
			13 => array( 'id' => 14, 'name' => 'American Beauty', 'pid' => 11),
			14 => array( 'id' => 15, 'name' => 'Lord of rings', 'pid' => 11),
			15 => array( 'id' => 20, 'name' => 'Spinderman III', 'pid' => 11),
			16 => array( 'id' => 19, 'name' => 'Logitech', 'pid' => 5),
			17 => array( 'id' => 16, 'name' => 'Creative', 'pid' => 5),
			18 => array( 'id' => 17, 'name' => 'www.Yahoo.com', 'pid' => 8),
			19 => array( 'id' => 18, 'name' => 'www.Hotmail.com', 'pid' => 8),
			20 => array( 'id' => 21, 'name' => 'Om Shanti Om', 'pid' => 10),
			21 => array( 'id' => 22, 'name' => 'Ji Ayaan Nu', 'pid' => 12),
			22 => array( 'id' => 23, 'name' => 'Music', 'pid' => 0)
		);
		$i = 0;			
		foreach( $arr1 as $row )
		{
			if ( $row['pid'] == $catid ) {
				$arr[$i]['id'] = $row['id'];
				$arr[$i]['name'] = $row['name'];
				$arr[$i]['pid'] = $row['pid'];
				$i ++ ;
			}
		}
	return $arr;
	}
}

if ( @$_REQUEST['method'] == 'getCat' )	{
	$objServices = new AjaxTreeview();
	$catid = isset($_REQUEST['catid'])?$_REQUEST['catid']:0;
	$arr = $objServices->getCatList($catid);
	$arrReturn['data'] = $arr;
	$arrReturn['id'] = @$_REQUEST['id'];
	$arrReturn['value'] = $catid;
	$jsonstring = json_encode($arrReturn);
	echo $jsonstring;
}
	
?>