<?php

class filter_ui {
    
    private function get_html_selected($field,$value) {
        if ( isset($_GET[$field]) && $_GET[$field] == $value )
            return ' selected="selected" ';
        return '';
    }
    private function get_html_option_and_selected($field,$value) {
        return ' value="'.$value.'" '.$this->get_html_selected($field,$value);
    }
    private function get_html_value($field) {
        if ( isset($_GET[$field]) )
            return $_GET[$field];
        return '';
    }
    public function __toString() {
        
        $result = '';
        $result .= '<form id="filtrado" method="get" action="'.html_template::get_php_self().'#filtrado" style="margin: 14px 0 0 8px; position:relative;">';
        
        $result .= '<select name="FILTER_tipo" id="FILTER_tipo" onchange="this.form.submit();">';
        $result .= '<option '.$this->get_html_option_and_selected('FILTER_tipo','').'>';
        $result .= 'Todas las iniciativas</option>';
        $result .= '<option '.$this->get_html_option_and_selected('FILTER_tipo',avotable::enum_tipo_ilp).'">';
        $result .= 'ILPs</option>';
        $result .= '<option '.$this->get_html_option_and_selected('FILTER_tipo',avotable::enum_tipo_propuesta).'">';
        $result .= 'Propuestas</option>';
        $result .= '<option '.$this->get_html_option_and_selected('FILTER_tipo',avotable::enum_tipo_pregunta).'">';
        $result .= 'Preguntas</option></select> &nbsp;&nbsp;';
        
        $result .= '<select name="FILTER_estado" id="FILTER_tipo" onchange="this.form.submit();">';
        $result .= '<option '.$this->get_html_option_and_selected('FILTER_estado','').'>';
        $result .= 'Todos los estados</option>';
        $result .= '<option '.$this->get_html_option_and_selected('FILTER_estado',avotable::enum_estado_admitida).'">';
        $result .= 'Activas</option>';
        $result .= '<option '.$this->get_html_option_and_selected('FILTER_estado',avotable::enum_estado_cerrada).'">';
        $result .= 'Cerradas</option>';
        $result .= '<option '.$this->get_html_option_and_selected('FILTER_estado',avotable::enum_estado_exitosa).'">';
        $result .= 'Exitosas</option>';             
        $result .= '</select> &nbsp;&nbsp;';
        
        $result .= '<input type="hidden" name="order_column" id="order_column" value="fecha_cerrada" />';
        
        
        $result .= '<select name="order_order" id="order_order" onchange="this.form.submit();">';
        $result .= '<option '.$this->get_html_option_and_selected('order_order','ASC').'>';
        $result .= 'Orden finalización descencente</option>';
        $result .= '<option '.$this->get_html_option_and_selected('order_order','DESC').'>';
        $result .= 'Orden finalización ascendente</option>';
        
        $result .= '</select> &nbsp;&nbsp;';
        
        $result .= 'Búsqueda: <input type="text" name="q" id="q" style="width:210px;" ';
        $result .= 'value="'.$this->get_html_value('q').'" ';
        $result .= '/> <input type="submit" value="Buscar" /> ';
        //$result .= 'Ordenar por fecha de lanzamiento: <a href="#">Acendente</a> / <a href="#">Descendente</a><br />';
        //$result .= 'Ordenar por fecha de finalización: <a href="#">Acendente</a> / <a href="#">Descendente</a><br />';
        $result .= '<a href="#" onclick="document.getElementById(\'q\').value=\'\';return false;" ';
        $result .= 'style="position:absolute; top:3px; right: 72px; text-decoration:none; color: #bbb">X</a>';
        $result .= '</form>';
        return $result;
    }
}