//-------------------------------------------
function writeCookieHideMenu(hide)
{
    var the_date = new Date("December 31, 2023");
    var the_cookie_date = the_date.toGMTString();
    var the_cookie = "menu_foldable_hide_menu="+hide;
    var the_cookie = the_cookie + ";expires=" + the_cookie_date+'; path=/;';
    document.cookie=the_cookie
}
function hide_menu() {
    close_peek_menu();
	document.getElementById('hide_menu_div').style.display="none";
	document.getElementById('show_menu_div').style.display="block";
    document.getElementById('menu_v').style.display="none";
	//document.getElementById('contenido').style.width="990px";
    writeCookieHideMenu(1);
    return false;
}
function show_menu() {
    close_peek_menu();
	document.getElementById('hide_menu_div').style.display="block";
	document.getElementById('show_menu_div').style.display="none";
	document.getElementById('menu_v').style.display="block";
	//document.getElementById('contenido').style.width="780px";
    writeCookieHideMenu(0);	
    return false;
}
function peek_menu() {
	document.getElementById('peek_menu_div').style.display="block";
}
function close_peek_menu() {
    document.getElementById('peek_menu_div').style.display="none";

}
function switch_peek_menu() {
    if (document.getElementById('peek_menu_div').style.display=="block") {
        close_peek_menu();
    } else {
        peek_menu();
    }
}