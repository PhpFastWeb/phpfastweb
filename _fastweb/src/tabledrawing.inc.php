<?php
echo "<script type=\"text/javascript\" >\n";
echo "var _php_enabled = true;\n";
echo "</script>\n";

function opentable($img_prefix, $corner_width, $corner_height, $width='', $height='') {
  $t_style = '';
  if ( $width != '' ) {
    $t_style = 'width: ' . $width . 'px;';
  }
  if ( $height != '' ) {
    $t_style = $t_style . 'height: ' . $height . 'px;';
  }
  $t_style = ' style = " ' . $t_style . ' " ';
  //alert(t_style);
  $nl = "\n";
  $result = '<table border="0" cellpadding="0" cellspacing="0" align="center" ' . $t_style . ' >';
  $result.=$nl.'<tr>';
  $result.=$nl.'<td style="width:' . $corner_width . 'px; height:' . $corner_height . 'px; background-image:url(' . $img_prefix . 'tl.jpg);"><img alt=" " src="' . $img_prefix . 'spacer.gif" border="0" /></td>';
  $result.=$nl.'<td style="height:' . $corner_height . 'px; background-image:url(' . $img_prefix . 't.jpg);background-repeat:repeat-x;"><img alt=" " src="' . $img_prefix . 'spacer.gif" border="0" /></td>';
  $result.=$nl.'<td style="width:' . $corner_width . 'px; height:' . $corner_height . 'px; background-image:url(' . $img_prefix . 'tr.jpg);"><img alt=" " src="' . $img_prefix . 'spacer.gif" border="0" /></td>';
  $result.=$nl.'</tr><tr>';
  $result.=$nl.'<td style="width:' . $corner_width . 'px; background-image:url(' . $img_prefix . 'l.jpg);"><img alt=" " src="' . $img_prefix . 'spacer.gif" border="0" /></td>';
  $result.=$nl.'<td>';
  
  return "-->".$result."<!--";
}

function closetable($img_prefix, $corner_width, $corner_height, $html_middle='') {
  $nl = "\n";
  $result ='</td>';
  $result .=$nl.'<td style="width:' . $corner_width . 'px; background-image:url(' . $img_prefix . 'r.jpg);"><img alt=" " src="' . $img_prefix . 'spacer.gif" border="0" /></td>';
  $result .=$nl.'</tr><tr>';
  $result .=$nl.'<td style="width:' . $corner_width . 'px; height:' . $corner_height . 'px; background-image:url(' . $img_prefix . 'bl.jpg);"><img alt=" " src="' . $img_prefix . 'spacer.gif" border="0" /></td>';
  $result .=$nl.'<td style="height:' . $corner_height . 'px; background-image:url(' . $img_prefix . 'b.jpg);"><img alt=" " src="' . $img_prefix . 'spacer.gif" border="0" />';
  if ( $html_middle != '' ) {
    $result .=$nl. html_middle;
  }
  $result .=$nl.'</td><td style="width:' . $corner_width . 'px; height:' . $corner_height . 'px; background-image:url(' . $img_prefix . 'br.jpg);"><img alt=" " src="' . $img_prefix . 'spacer.gif" border="0" /></td>';
  $result .=$nl.'</tr>';
  $result .=$nl.'</table>';
  return "-->".$result."<!--";
}
