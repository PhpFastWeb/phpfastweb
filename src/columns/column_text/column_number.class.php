<?php
class column_number extends column_text {
	/**
	 * @param icolumn $col
	 * @return column_number
	 */

	public function __construct() {
		parent::__construct();
	}
	public static function &cast(icolumn $col){
		if ( ! $col instanceof column_number) throw new ExceptionDeveloper("El objeto no es de la clase adecuada");
		return $col;
	}
	
	const type_numeric = 1;
	const type_integer = 2;
	const type_float = 3;
	/**
	 * @var int
	 */
	protected $type = column_number::type_numeric;
	/**
	 * Devuelve constante del tipo numérico que acepta
	 * @return int
	 */
	public function get_type() {
		return $this->type;
	}
	/**
	 * Establece el tipo numérico que acepta, 'type_numeric' por defecto
	 * @param int $type
	 * @return column_number
	 */
	public function &set_type($type) {
		$this->type = $type;
		return $this;
	}
	/**
	 * @var string
	 */
	protected $unit = '';
	public function &set_unit($unit) {
		$this->unit = $unit;
		return $this;
	}
	public function get_unit() {
		return $this->unit;
	}
	
	
	protected $show_unit_on_render = true;
	public function get_show_unit_on_render() {
		return $this->show_unit_on_render;
	}
	/**
	 * Sets if unit is shown on render (it's allways shown when on input)
	 * @param $show_unit 
	 * @return column_number
	 */
	public function &set_show_unit_on_render($show_unit=true) {
		$this->show_unit_on_render = $show_unit;
		return $this;
	}
	
	protected $zero_nulls = true;
	public function &set_zero_nulls($zero_nulls=true) {
		$this->zero_nulls = $zero_nulls;
		return $this;
	}
	public function get_zero_nulls() {
		return $this->zero_nulls;
	}
	
	public function get_formatted_value() {
		$result = parent::get_formatted_value();
		if ($this->value == 0 && $this->get_zero_nulls()) {
			$result = "";
		}
		if ($result != "" && $this->unit != '') {
			$result = $result . " " . $this->unit;
		}
		return $result;
	}
	public function get_input_plain() {
		
		$ss = "";
			if ($this->width != '') {
			$ss = "style=\"text-align:right;width:$this->width;\"";
		} else {
			 $ss = "style=\"text-align:right;\""; 
		}

		if ($this->value == "0" && $this->get_zero_nulls()) {
			$value = "";
		} else {
			$value = $this->get_value(); 
			//TODO: Definir un nuevo tipo de value, ya que formatted añade la unidad al final para representaciones no input			
		}
		
		$js_onchange = $this->get_js_onchange();
        $autocomplete = $this->get_table()->autocomplete ? '' : 'autocomplete="off"';
		$result = "<input type=\"text\" class=\"form_edit_row_inputtext form_edit_row_inputnumber\" name=\"{$this->get_html_id()}\" id=\"{$this->get_html_id()}\" value=\"$value\" $ss $js_onchange $autocomplete /> ";
			if ($this->unit != '') {
			$result = $result . ' <span class="form_edit_row_unit">' . $this->unit . '</span>';
		}
		return $result;
	}

	
	public function validate() {		
		parent::validate ();
		if ($this->value=="") return $this->validates;
		switch ($this->type) {
			case column_number::type_numeric:
				if ( ! is_numeric($this->value)) {
					$this->validates = false;
					$this->validation_messages[] = "Formato numérico no valido para el valor: ".$this->value;
				}
				return $this->validates;
			case column_number::type_integer:
				
				if ( ! is_numeric($this->value) || (int)$this->value != $this->value ) {
					$this->validates = false;
					$this->validation_messages[] = "Formato entero no valido para el valor: ".$this->value;
				}				
				return $this->validates;
			case column_number::type_float:
				if ( ! is_numeric($this->value) || (float)$this->value != $this->value ) {
					$this->validates = false;
					$this->validation_messages[] = "Formato decimal coma flotante no valido para el valor: ".$this->value;
				}					
				return $this->validates;
			default:
				throw new exception ("Definición de tipo numérico no válida");
				break;
		}
		return $this->validates;
	}
	public function get_db_type() {
		return 'int(32)';
	}
}