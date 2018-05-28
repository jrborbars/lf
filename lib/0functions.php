<?php
#######################################################################
# FUNCTIONS
#######################################################################
function groupMens($msg1){
	$mens = array(
			'e' => $msg1->display('e',false),	// Error
			'w' => $msg1->display('w',false),	// Warning
			'i' => $msg1->display('i',false),	// Information
			's' => $msg1->display('s',false)	// 
		);
	return $mens;
} // groupMens

function urlRoute(){
	$local = $_SERVER['REQUEST_URI'];
	//$url = explode('/', $local);
	return $local;
} // urlRoute

function urlLevel(){
	$level = '';
	$local = $_SERVER['REQUEST_URI'];
	$url = explode('/', $local);
	foreach ($url as $u){
		$level .= '../';
	}
	return $level;
} // urlLevel

function createCsrf(){
	$name = \Volnix\CSRF\CSRF::TOKEN_NAME;
	$value = \Volnix\CSRF\CSRF::getToken();
	return array( $name => $value );
}

function pagination($tbl='',$pag,$msg, $log ,$sort=''){
	$DOCS = 10; // rows to show in table
	$LNK = 2; // # elements before/after middle element
	$dados = array();
	$count = 0;
	$pag = (int)$pag;

	if ($pag<=0 || gettype($pag)!='integer' || $pag === null){
		$pag=1;
	}
	if ($tbl!='' && gettype($tbl) === 'string'){
		try {
			$count = Model::factory($tbl)->count();
		} catch (PDOException $e) {
			$msg->error('Problemas no acesso aos dados. ', null, true);
			$log->addError('Route: '.urlRoute().' Problemas no acesso aos dados. '.$e->getMessage());
		} catch (Exception $e) {
			$msg->error('Problema. ', null, true);	
			$log->addError('Route: '.urlRoute().' Problemas no acesso aos dados. '.$e->getMessage());
		}
	} else {
		$msg->error('Não foram encontrados registros!', null, true);
		$log->addError('Route: '.urlRoute().' Problemas no acesso aos dados. '.$e->getMessage());
	}

	$lastpag  = (int)ceil($count / $DOCS);
	( $pag > $lastpag ) ? $pag = $lastpag : $pag = $pag;
		
	$skip  = ($pag - 1) * $DOCS;
	if ($tbl!='' && gettype($tbl) === 'string'){
		try {
			$dados = Model::factory($tbl)->limit($DOCS)->offset($skip)->find_many();
			//$count = count($dados);
		} catch (PDOException $e) {
				$msg->error('Problemas no acesso aos dados. ', null, true);
				$log->addError('Route: '.urlRoute().' Problemas no acesso aos dados. '.$e->getMessage());
		} catch (Exception $e) {
				$msg->error('Problema. ', null, true);
				$log->addError('Route: '.urlRoute().' Problemas no acesso aos dados. '.$e->getMessage());
		}
	} else {
		throw new Exception('Table not defined.');
		$log->addError('Route: '.urlRoute().' Table not defined. '.$e->getMessage());
	}
//	$output = ORM::for_table('sample')
//  ->distinct()
//  ->raw_query('SELECT id,name,timestamp FROM sample GROUP BY name',array())
//  ->find_many();

	$startpag = (( $pag - $LNK ) > 0 ) ? $pag - $LNK : 1;
	$endpag   = (( $pag + $LNK ) < $lastpag ) ? $pag + $LNK : $lastpag;
	$pags=array();
	for ($i=$startpag;$i<=$endpag;$i++){
	  	$pags[]=$i;
	}
 
	$inireg = $skip + 1;
	$fimreg = (( $skip + $DOCS ) < $count ) ? $skip + $DOCS : $count;
   	$firstclass = ($pag == 1) ? "disabled" : "";
   	$lastclass  = ($pag == $lastpag ) ? "disabled" : "";

	$pagina = array(
			'firstclass'=> $firstclass,
			'lastclass' => $lastclass,
			'docs'		=> $DOCS,
			'skip'		=> $skip,
			'pag'		=> $pag,
			'pags'		=> $pags,
			'startpag'	=> $startpag,
			'count' 	=> $count,
			'ini'		=> $inireg,
			'fim'		=> $fimreg,
			'ult'		=> $lastpag,
			'dados'		=> $dados
			);
	return $pagina;
} // pagination


function validation($validate_route = '',$validate_operation = ''){
	if ($validate_route == 'usu' && $validate_operation == 'ins'){
		// regras de validacão
		$val = array(
		    'senha'    		=> 'required|max_len,100|min_len,8',
		    'senha1'   		=> 'required|max_len,100|min_len,8|match,senha',
		    'email'    		=> 'required|valid_email',
		    'nome'     		=> 'required|alpha_space|max_len,100|min_len,5',
		    'ativo'      	=> 'exact_len,1|contains,0 1'
		);
		// filtros para os dados
		$filter = array(
		    'nome' 			=> 'trim|sanitize_string',
		    'senha' 		=> 'trim',
		    'senha1'    	=> 'trim',
		    'email'    		=> 'trim|sanitize_email',
		    'ativo'      	=> 'trim'
		);
	} 
	if ($validate_route == 'usu' && $validate_operation == 'alt'){
		// regras de validacão
		$val = array(
		    'email'    		=> 'required|valid_email',
		    'nome'     		=> 'required|alpha_space|max_len,100|min_len,5',
		    'ativo'      	=> 'contains, on off'
		);
		// filtros para os dados
		$filter = array(
		    'nome' 			=> 'trim|sanitize_string',
		    'email'    		=> 'trim|sanitize_email',
		    'ativo'      	=> 'trim'
		);
	}
	if ($validate_route == 'usu' && $validate_operation == 'lgi'){ #login
		// regras de validacão
		$val = array(
	    	'usuario'    	=> 'required|valid_email',
		    'senha'    		=> 'required|max_len,100|min_len,8'
		);
		// filtros para os dados
		$filter = array(
	    	'usuario'		=> 'trim|sanitize_string',
	    	'senha' 		=> 'trim'
		);
	}
	if ($validate_route == 'register'){
		// regras de validacão
		$val = array(
	    	'nome'  	 	=> 'required|max_len,255|min_len,4',
		    'email'    		=> 'required|valid_email'
		);
		// filtros para os dados
		$filter = array(
	    	'nome'			=> 'trim',
	    	'email' 		=> 'trim|sanitize_string'
		);
	}
	$res1 = array($val,$filter);
	return $res1;
}
###############################################################################

function envia_email($n,$e,$ehash){
	
	$mail = new PHPMailer();	// instancia a classe							// Instancia a Classe
	$mail->SMTPDebug = 5;	// 0-5 nível das mensagens de debug                               // Enable verbose debug output
	$mail->Debugoutput = 'html';	// ou 'text'                          // Set mailer to use SMTP
	$mail->isSMTP();     // Usa servidores SMTP                         // Set mailer to use SMTP
	$mail->Host = ''; // endereco SMTP 					// Specify main and backup SMTP servers 'smtp1.example.com;smtp2.example.com'
	$mail->SMTPAuth = true;	// necessita autorizar                               // Enable SMTP authentication
	$mail->Username = '';         	  // SMTP username
	$mail->Password ='';                           // SMTP password
	$mail->SMTPSecure = 'tls'; // seg. de autenticacao                          // Enable TLS encryption, `ssl` also accepted
	$mail->Port = 25;   // porta do servidor SMTP                                 // TCP port to connect to
	
	// QUEM ENVIA
	$mail->From = '';// email
	$mail->FromName = 'Mailer Test'; // nome
	
	// QUEM RECEBE
	// email e nome
	$mail->addAddress('', 'Joe User');     // Add a recipient
	#$mail->addAddress('ellen@example.com');
	
	// responder para:
	$mail->addReplyTo('', 'Joe Information');
	
	// cópias: normais (CC) ou ocultas (BCC)
	#$mail->addCC('cc@example.com');
	#$mail->addBCC('bcc@example.com');
	// anexos
	$mail->addAttachment('g4968.png');  // Anexo                // Add attachments
	#$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
	
	// email em html (permite formatacao) ou texto (texto puro)
	$mail->isHTML(true);                                  // Set email format to HTML
	// assunto e corpo da mensagem
	$mail->Subject = '[MNF9] Email de teste';
	$mail->Body    = 'Fala aí, Bro! <b>Tudo Trankx?</b>
						Pode colocar <i>qualquer</i> HTML
						<a href="www.google.com.br">aqui
						</a>!';
	$mail->AltBody = 'Este texto aqui é opcional,
						e é utilizado caso o cliente 
						de email não aceite HTML
						(somente texto).';
	
	if(!$mail->send()) {
	    //echo 'A mensagem não pôde ser enviada.';
	    //echo 'Erro do Mailer: ' . $mail->ErrorInfo;
	    $retorno = false;
	} else {
		$retorno = true;
	    //echo 'A mensagem foi enviada! <b>BRAVO!</b>';
	}
	return $retorno;
}