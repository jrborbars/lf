<?php
#######################################################################
# CLASSES
#######################################################################

class ValidateInput{

    private $dados;
    private $mypost;
    public function validate($param){
    	$this->dados = $param;
		$mypost = $gump->sanitize($_POST);
		$f = validation($this->dados);
		// filtra e valida os dados
		$gump->validation_rules($f[0]);
		$gump->filter_rules($f[1]);
		return $gump->run($mypost);
   }
} // EOC