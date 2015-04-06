<?php

require_once(dirname(__FILE__)."/table_data_atributes.class.php");


class table_data_list extends table_data_atributes {

	function print_can_edit() {
		if ( $this->can_edit_use ) {
			$can_edit = ( process_session_var('edit') == 'on' );
			if ( ! isset($_GET[$this->action_command]) && ! isset($_POST[$this->action_command]) ) {
				$change_edit[true]='off';
				$change_edit[false]='on';
				$change_edit_text[true]='Desactivar Ediciï¿½n';
				$change_edit_text[false]='Activar Ediciï¿½n';
				$edit_text[true]='<b>Editando</b> &nbsp;&nbsp;';
				$edit_text[false]='<b>No Editando</b> &nbsp;&nbsp;';
				$link=html_template::get_php_self()."?edit=".$change_edit[$can_edit];
				$text=$change_edit_text[$can_edit];				
				echo $edit_text[$can_edit]."<a href=\"$link\" class=\"link_button\">$text</a>";			
				if ($can_edit) { echo ' &nbsp;| &nbsp;'; }
				else { echo "<hr class=\"list_item_separator\" />"; }
			}
		}
	}
	function print_list() {
		$this->init_config();
		
		
		if ($this->table_data == null ) $this->fetch_data();
		echo "<div style=\"width:215px;float:right;clear:right;\">";
		$this->print_list_navegation();
		$this->print_list_search();
		echo "</div>";
		
		$this->print_can_edit();
		if ( $this->print_actions || $this->print_action_new ) {
			echo "<a href=\"".html_template::get_php_self()."?action=NEW\" class=\"link_button\" >";
			echo "<img src=\"".$this->theme->get_img_dir()."/icon_new.gif\" align=\"middle\" alt=\"Nuevo\" /> ";
			echo "Nuevo</a>";			
			//$this->print_link_button(html_template::get_php_self()."?action=NEW",'Nuevo','Nuevo');
			
			echo "<hr class=\"list_item_separator\" />";
			echo "<br />\r\n";
		}
		if (isset($_GET['q'])) {
			$this->print_list_caption();
		}
		$i=0;
		$row = website::$database->fetch_result($this->table_data);
		while( $row ) {
			$item = new list_item( $row );
			$item->theme = $this->theme;
			$item->table = $this;
			$item->view_details = $this->list_view_details;
			$item->fields_resume = $this->list_fields_resume;
			$item->print_item();
			if ( $this->print_actions ) {
				$this->print_list_actions( $row );
			}
			echo '<br clear="left" />';
			$i++;
			$row = website::$database->fetch_result($this->table_data);
		}
		if ($i==0) {
			$item = new list_item();
			$item->fields_header[]="No hay datos";
			$item->theme = $this->theme;
			$item->print_title();
		}
	}
	function print_list_item($pk) {
		echo "<div style=\"background-color: #D4FFAA; width: 472px; margin-bottom: 8px; padding: 4px 4px 4px 4px;\" >";
		echo "Examinando item $pk. <a href=\"#\" onclick=\"javascript:location='".html_template::get_php_self()."';\" >Ver todas</a>.";
		echo "</div>";
		$row = $this->fetch_row($pk);
		$item = new list_item($row,$this);
		$item->theme = $this->theme;
		$item->print_item();
		if ( $this->print_actions ) {
			$this->print_list_actions( $row );
		}		
	}
	function print_list_caption() {
		echo "<div style=\"background-color: #D4FFAA; width: 472px; margin-bottom: 8px; padding: 4px 4px 4px 4px;\" >";
		echo "Buscando: ".$_GET['q']."<br />";
		echo "<a href=\"#\" onclick=\"javascript:document.location='".html_template::get_php_self()."';\">";
		echo "Restablecer listado";
		echo "</a>";
		echo "</div>";
	}
	function print_list_navegation() {
		echo "<div class=\"list_navegation\">\r\n";
		echo "PaginaciÃ³n:<br />";
		$this->print_pag_icons();
		echo "</div>\r\n";
	}
	function print_list_search() {
		echo "<div class=\"list_search\">\r\n";
		echo "<form style=\"margin: 0 0 0 0;\" action=\"".html_template::get_php_self()."\" method=\"get\">";
		echo "Buscar:<br />";
		echo "<input type=\"text\" name=\"q\" id=\"q\" ";
		if (isset($_GET['q'])) {
			echo "value = \"".$_GET['q']."\" ";
		}
		echo "/><br />";
		echo "<input style=\"font-size:9px; height: 24px; \" type=\"button\" name=\"reset\" id=\"reset\" value=\"Restablecer\" onclick=\"javascript:document.location='".html_template::get_php_self()."';\" ";
		if (!isset($_GET['q'])) {
			echo "disabled=\"disabled\" ";
		}
		echo "/> ";
		echo "<input type=\"submit\" value=\"   Buscar   \" />";
		echo "</form>";
		echo "</div>\r\n";
	}	
	function print_list_actions($row) {
		if ($this->print_actions) {
			$pkstr = url::$url_separator.$this->key_set->primary_keys[0].'='.$row[$this->key_set->primary_keys[0]];
			//TODO: Permitir más de una primary key
			echo "<a href=\"".html_template::get_php_self()."?action=EDIT$pkstr\" class=\"link_button\" >";
			echo "<img src=\"".$this->theme->get_img_dir()."/icon_edit.gif\" align=\"middle\" /> ";
			echo "Editar</a>";
			echo " ";
			echo "<a href=\"".html_template::get_php_self()."?action=DELETE$pkstr\" class=\"link_button\" >";
			echo "<img src=\"".$this->theme->get_img_dir()."/icon_delete.gif\" align=\"middle\"  /> ";
			echo "Borrar</a>";			
		}
		echo "<hr class=\"list_item_separator\" />";	
		//echo '<br />';	
	}	
	
}
