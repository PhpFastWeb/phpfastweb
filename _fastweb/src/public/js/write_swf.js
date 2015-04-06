//Cambios:
// 16 Nov 2005:
// añadido parámetro 'transparent' para bgcolor

function write_swf(swf_file,width,heigth,bgcolor,id,style) {
  if (!id) {
    id='id';
  }
  if (!style) style="";
  document.write('<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" ');
  document.write('width="' + width +'" ');
  document.write('height="' + heigth + '" ');
  document.write('id="' + id + '" ');
  document.write('style="' + style + '" >'); 

  document.write('<param name="allowScriptAccess" value="sameDomain" />');
  document.write('<param name="movie" value="' + swf_file + '" />');
  document.write('<param name="quality" value="high" />');
  if (bgcolor=='transparet') {
    document.write('<param name="wmode" value="transparent">');
  } else {
    document.write('<param name="bgcolor" value="' + bgcolor + '" />');
  }

  document.write('<embed src="' + swf_file + '" ' );
  document.write('quality="high" ');
  if (bgcolor=='transparet') {
    document.write('wmode="transparent" ');
  } else {
  document.write('bgcolor="' + bgcolor + '" ');
  }
  document.write('width="' + width +'" '); 
  document.write('height="' + heigth + '" ');
  document.write('name="'+ id +'" ');
  document.write('align="middle" allowScriptAccess="sameDomain"');
  document.write('type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />');

  document.write('</object>');
}
