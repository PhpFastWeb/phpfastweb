<?php
	class command_send_file extends acommand_internal implements icommand {
	public function get_key() {
		return 'send_file';
	}
	public function execute() {
		$this->send_download();
	}
	function send_download() {
		if ( ! $this->upload_use_redirect ) return;
		$pk = $_GET[$this->key_set->primary_keys[0]];
		//TODO: Permitir más de una primary_key
		$upload = new upload('',$this->upload_dir,0,$pk);
		$upload->file_name = $_GET['file'];
		$upload->pk_to_name = $this->upload_use_pk;
		$upload->pk_compatibility = $this->upload_detect_old_non_pk;
		$upload->send_download();
	}
		
	}
?>