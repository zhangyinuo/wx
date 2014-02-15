<?php
	function check_form($form) {
		return $form;
		if(!get_magic_quotes_gpc()) {
			for($i=0;$i<count($form);$i++) {
				$form[$i] = addslashes($form[$i]);
			}
		}
		return $form;
	}
?>
