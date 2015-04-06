<?php

class data_process_unit {
	
	
	public $data;
	/**
	 * Devuelve si al procesar los datos se encontró una condición de error para no realizar actualización en la base de datos.
	 *
	 * @var bool
	 */
	public $error;
	/**
	 * Mensaje/s de error encontrado/s
	 *
	 * @var string
	 */
	public $error_msg;
	
	public $pk = array();
	public $pk_prev = array();
	public $pk_set;
	public $pk_change;
	public $action;
	
}