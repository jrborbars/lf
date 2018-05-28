<?php
// route_users.php
// "Auth" at the end of this file
$tbl = 'users';
$route_prefix = 'admin/';
$template_prefix='usuarios/';
#######################################################################################
#
#	INSERT
#
#######################################################################################
$app->route('GET /'.$route_prefix.'usuarios/ins', function() use ($twig, $log, $msg, $tbl,$template_prefix){
	$role=0; // 0-admin .. 9-logged user
	$auth = new Auth();
	$auth->check_logRole($auth, $role);
	$log->addInfo('Route: '.urlRoute());
	$data = array(
			'atualpag' 	=> urlRoute(), 
			'menu' 		=> $menu = array(),
	        'level'     => urlLevel(),
	        'csrf'		=> createCsrf(),
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render($template_prefix.'usuarioins.html', $data);
});

$app->route('POST /'.$route_prefix.'usuarios/ins', function() use ($app, $twig, $log, $msg, $tbl,$gump){
	$role=0; // 0-admin .. 9-logged user
	$auth = new Auth();
	$auth->check_logRole($auth, $role);
	$error = '';
	$log->addInfo('Route: POST '.urlRoute());
	$postdata = \Volnix\CSRF\CSRF::validate($_POST);
	$ok=null;
    if ($postdata) { // CSRF valid
		try {
			$dado = Model::factory($tbl)->create();
			$campos = Model::factory('COLUMNS', 'alternate')->where('TABLE_NAME', $tbl)->find_array();
			//if (!isset($_POST['ativo'])){
				$_POST['ativo'] = 0;
			//}
			$_POST['created'] = date('Y-m-d H:i:s',time()); # H:24h h:12h
			$_POST['updated'] = date('Y-m-d H:i:s',time());
			#############################################################
			#    validacao dos dados
			#############################################################
			// You don't have to sanitize, but it's safest to do so.
			$mypost = $gump->sanitize($_POST);
			$res = validation('usu','ins'); // return two arrays. First for validation, second for filter
			// filtra e valida os dados
			$gump->validation_rules($res[0]);
			$gump->filter_rules($res[1]);
			$valido = $gump->run($mypost);
			
			#############################################################
			if ($valido){
				foreach($campos as $campo){
						// não atribui aos campos listados no IF
						if ( $campo['COLUMN_NAME'] != 'id' && $campo['COLUMN_NAME'] != 'email_hash'&& $campo['COLUMN_NAME'] != 'role' ){ 
							$dado->{$campo['COLUMN_NAME']} = $mypost["{$campo['COLUMN_NAME']}"];
						}
				}
				$hashopt = [ 'cost' => 12]; // default = 10
				$ehash = password_hash($mypost['senha'], PASSWORD_DEFAULT,$hashopt);
				$dado->senha = $ehash;
				$log->addError('Usuario: TESTE \t\t  Inserted data: '.serialize($dado));
				$ok = $dado->save();
			} else {
				$error .= $gump->get_readable_errors(true);
				$msg->error('Problemas no processamento do formulário. Dados inválidos. '.$error);
				$app->redirect('/admin/usuarios/1');
			}
		} catch (Throwable $t){
			$error .= $t->getMessage()."//Threx";	
		} catch (PDOException $e){
			$error .= $e->getMessage()."//PDOex";
		} catch (Exception $e){
			$error .= $e->getMessage()."//Genex";	
		}
		if ($ok>=1 && empty($error)){
			$msg->success('Inserido com sucesso!');
			$log->addInfo('Route: POST '.urlRoute().' Inserido com sucesso  DATA:'.serialize($_POST));
		} else {
			$msg->error('Problemas na inserção. '.$error);
			$log->addError('Route: '.urlRoute().' Error: Problemas na inserção.'.$error.'  DATA:'.serialize($_POST));
		}
		$app->redirect('/admin/usuarios/1');
    } else {
		$msg->error('Problemas no processamento do formulario-ins. Possible CSRF attack from: '.filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP).' <br/>'.$error);
		$log->addError('Route: '.urlRoute().' Possible CSRF attack from: '.filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP).'  DATA:'.serialize($_POST));
		$app->redirect('/admin/usuarios');
    }
});
#######################################################################################
#
#	ALTERA
#
#######################################################################################
$app->route('GET /admin/usuarios/alt/@id', function($id) use ($twig, $log, $msg, $tbl){
	$role=0; // 0-admin .. 9-logged user
	$auth = new Auth();
	$auth->check_logRole($auth, $role);
	$id = (int)$id;
	$log->addInfo('Route: '.urlRoute());
	$dados = Model::factory($tbl)->where('id',$id)->find_one();
	$data = array(
			'atualpag' 	=> urlRoute(), 
			'menu' 		=> $menu = array(),
	        'level'     => urlLevel(),
	        'csrf'		=> createCsrf(),
			'dados'		=> $dados,
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render('usuarios/usuarioalt.html', $data);
});

$app->route('POST /admin/usuarios/alt', function() use ($app, $twig, $log, $msg, $tbl,$gump){
	$role=0; // 0-admin .. 9-logged user
	$auth = new Auth();
	$auth->check_logRole($auth, $role);
	$error = '';
	$log->addInfo('Route: '.urlRoute());
	$postdata = \Volnix\CSRF\CSRF::validate($_POST);
	$campos = Model::factory('COLUMNS', 'alternate')->where('TABLE_NAME', $tbl)->find_array();
	$ok=null;
    if ($postdata) { // CSRF valid
		try {
			#$_POST['updated'] = date('Y-m-d h:i:s',time());
			#############################################################
			#    validacao dos dados
			#############################################################
			$mypost = $gump->sanitize($_POST);
			$res = validation('usu','alt');
			// filtra e valida os dados
			$gump->validation_rules($res[0]);
			$gump->filter_rules($res[1]);
			$valido = $gump->run($mypost);
			$mypost['ativo']=1;
			$mypost['updated'] = date('Y-m-d H:i:s',time());
			#############################################################
			if ($valido){
				$id = $mypost['id'];
				$dado = Model::factory($tbl)->where('id',$id)->find_one();
				foreach($campos as $campo){
					// não atribui aos campos listados no IF
					if ( $campo['COLUMN_NAME'] != 'email_hash' && $campo['COLUMN_NAME'] != 'senha' && $campo['COLUMN_NAME'] != 'created' && $campo['COLUMN_NAME'] != 'role'){ 
						$dado->{$campo['COLUMN_NAME']} = $mypost["{$campo['COLUMN_NAME']}"];
					}
				}
				$log->addNotice('Route: POST '.urlRoute().'[Usuario]: TESTE Modified data: '.serialize($dado));
				$ok = $dado->save();
			} else {
				$error .= $gump->get_readable_errors(true);
				$msg->error('Problemas no processamento do formulario-alt. Dados inválidos. '.$error);
				$app->redirect('/admin/usuarios/1');
			}
		} catch (Throwable $t){
			$error .= $t->getMessage()."Threx";	
		} catch (PDOException $e){
			$error .= $e->getMessage()."PDOex";
		} catch (Exception $e){
			$error .= $e->getMessage()."Genex";	
		}
		if ($ok>=1 && empty($error)){
			$msg->success('Alterado com sucesso!');
		} else {
			$msg->error('Problemas na alteracao.'.$error);
		}
		$app->redirect('/admin/usuarios/1');
    } else {
		$msg->error('Problemas no processamento do formulario-alt. Possible CSRF attack from: '.filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP).' <br/>'.$error);
		$log->addError('Route: '.urlRoute().' Possible CSRF attack from: '.filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP).'  DATA:'.serialize($_POST));
		$app->redirect('/admin/usuarios');
    }
});
#######################################################################################
#
#	VER
#
#######################################################################################

$app->route('/admin/usuarios/ver/@id', function($id) use ($twig, $log, $msg, $tbl){
	$id = (int)$id;
	$log->addInfo('Route: '.urlRoute());
	$dados = Model::factory($tbl)->where('id', $id)->findOne();
	$menu = array(
		'Home' 			=> 'home',
		'Cadastros' 	=> 'cadastros',
		'Usuários' 	    => 'admin/usuarios',
		'Sobre' 		=> 'sobre',
		'Contato' 		=> 'contato',
		'Logout' 		=> 'logout');
	$data = array(
			'atualpag' 	=> urlRoute(), 
			'menu' 		=> $menu,
	        'level'     => urlLevel(), 
			'dados'		=> $dados,
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render('usuarios/usuariover.html', $data);
});
#######################################################################################
#
#	DELETA
#
#######################################################################################
$app->route('GET /admin/usuarios/del/@id', function($id) use ($twig, $log, $msg, $tbl){
	$id = (int)$id;
	$log->addInfo('Route: '.urlRoute());
	$dados = Model::factory($tbl)->where('id',$id)->findOne();
	$data = array(
			'atualpag' 	=> urlRoute(), 
			'menu' 		=> $menu = array(),
	        'level'     => urlLevel(), 
			'dados'		=> $dados,
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render('usuarios/usuariodel.html', $data);
});
$app->route('POST /admin/usuarios/del/@id', function($id) use ($app, $twig, $log, $msg, $tbl){
	$id = (int)$id;
	$error = '';
	$log->addInfo('Route: '.urlRoute());
	$dados = null;
	try {
		$dados = Model::factory($tbl)->where('id', $id)->findOne();
		$ok = $dados->delete();
	} catch (Throwable $t){
		$error .= $t->getMessage();	
	} catch (PDOException $e){
		$error .= $e->getMessage();
	} catch (Exception $e){
		$error .= $e->getMessage();	
	}
	if ($ok>=1){
		$msg->success('Deletado com sucesso!');
	} else {
		$msg->error('Problemas na deleção.'.$error);
	}
	$menu = array(
		'Home' 			=> 'home',
		'Cadastros' 	=> 'cadastros',
		'Usuários' 	    => 'admin/usuarios',
		'Sobre' 		=> 'sobre',
		'Contato' 		=> 'contato',
		'Logout' 		=> 'logout');
	$app->redirect('/admin/usuarios/1');
});
#######################################################################################
#
#	LISTAGEM
#
#######################################################################################
$app->route('/admin/usuarios', function() use($app) {
    $app->redirect('/admin/usuarios/1');
});

$app->route('/admin/usuarios/@pag', function($pag) use ($twig, $log, $msg, $tbl){
	$ordenacao = 'nome';
	$log->addInfo('Route: '.urlRoute());
	// tabela, pagina atual, obj_msg, obj_log, ordenacao
	$pagina = pagination($tbl,$pag,$msg, $log,$ordenacao);
	$dados = $pagina['dados'];
	unset($pagina['dados']);
	$menu = array(
		'Home' 			=> 'home',
		'Cadastros' 	=> 'cadastros',
		'Usuários' 	    => 'admin/usuarios',
		'Sobre' 		=> 'sobre',
		'Contato' 		=> 'contato',
		'Logout' 		=> 'logout');
	$data = array(
			'atualpag' 	=> urlRoute(), 
			'menu' 		=> $menu,
	        'level'     => urlLevel(),
			'dados'		=> $dados,
			'pagina' 	=> $pagina,
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render('usuarios/index.html', $data);
});

#######################################################################################
#######################################################################################
#######################################################################################
#######################################################################################
#
#	AUTH - Authenticate users
#
#######################################################################################


$app->route('GET /logout', function() use ($log, $msg, $app){
	$local = $_SERVER['REQUEST_URI'];
	session_destroy();
	$msg->info('Saiu do app com sucesso. Volte sempre!');
	$log->addInfo('Route: '.$local);
    $app->redirect('/login');
});

$app->route('GET /login', function() use ($twig, $log, $msg){
	$local = $_SERVER['REQUEST_URI'];
	$log->addInfo('Route: '.$local);
	$data = array(
		'atualpag' 	=> urlRoute(), 
		'menu' 		=> $menu = array(),
        'level'     => urlLevel(),
        'csrf'		=> createCsrf(),
		'mens' 		=> groupMens($msg)
		);
	echo $twig->render('usuarios/login.html', $data);
});

$app->route('POST /login', function() use ($app, $twig, $log, $msg, $tbl,$gump){
	$error = '';
	$log->addInfo('Route: '.urlRoute());
	$postdata = \Volnix\CSRF\CSRF::validate($_POST);
	$ok=null;
    if ($postdata) { // CSRF valid
		 try{
			#############################################################
			#    validacao dos dados
			#############################################################
			$mypost = $gump->sanitize($_POST);
			$res = validation('usu','lgi');
			// filtra e valida os dados
			$gump->validation_rules($res[0]);
			$gump->filter_rules($res[1]);
			$valido = $gump->run($mypost);
			#############################################################
			if ($valido){
				$count = Model::factory($tbl)->where('email',$mypost['usuario'])->count();
				if ($count > 0){
					$dado = Model::factory($tbl)->where('email',$mypost['usuario'])->find_one();
					if ($dado->ativo < 1){
						throw new Exception("User was not an active user. Contact admin for instructions."); 
					}
					$verif = password_verify($mypost['senha'],$dado->senha);
					if ($verif){
						$r = "OK";
						$ok=1;
						$phash = crypt($dado->nome, $dado->email);
						$_SESSION['login'] = $phash;
						$_SESSION['username'] = $dado->nome;
						$_SESSION['username']['role'] = $dado->role;
						$_SESSION['comm'] = sha256($dado->email);
						$_SESSION['comm']['raw'] = $dado->email;
						$_SESSION['inicio'] = time();	// carimbo de entrada no sistema
						$_SESSION['vida'] = 600; // 60s = 1min ==> tempo de permanência
					} else {
						$r = "NO";
						throw new Exception("User or password are invalid."); 
					}
					$log->addNotice('Route: POST '.urlRoute().'[Usuario]: TESTE - LOG:'.$r.'  Data: '.serialize($dado));
					$app->redirect('/dashboard');
					#ORM::get_last_query();
				} else {
					throw new Exception("User or password are invalid."); 
				}
			} else {
				$error .= $gump->get_readable_errors(true);
				$msg->error('Dados inválidos. '.$error);
				$app->redirect('/login');
			}
		} catch (Throwable $t){
			$error .= $t->getMessage()."Threx";	
		} catch (PDOException $e){
			$error .= $e->getMessage()."PDOex";
		} catch (Exception $e){
			$error .= $e->getMessage()."Genex";	
		}
		if ($ok>=1 && empty($error)){
			$msg->success('Logado com sucesso!');
			$app->redirect('/dashboard');
		} else {
			$msg->error('Problemas no Log.'.$error);
			$app->redirect('/login');
		}
		
    } else {
		$msg->error('Problemas no processamento do formulario-log. Possible CSRF attack from: '.filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP).' <br/>'.$error);
		$log->addError('Route: '.urlRoute().' Possible CSRF attack from: '.filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP).'  DATA:'.serialize($_POST));
		$app->redirect('/login');
    }
	
});


$app->route('GET /register', function() use ($twig){
	$data = array(
		'atualpag' 	=> urlRoute(), 
		'menu' 		=> $menu = array(),
        'level'     => urlLevel(),
        'csrf'		=> createCsrf(),
		'mens' 		=> groupMens($msg)
		);
	echo $twig->render('auth/register.html', $data);
});
$app->route('POST /register', function() use ($twig, $app){
	$postdata = \Volnix\CSRF\CSRF::validate($_POST);
	$ok=null;
	$error='';
    if ($postdata) { // CSRF valid
		#############################################################
		#    validacao dos dados
		#############################################################
		$mypost = $gump->sanitize($_POST);
		$res = validation('register');
		// filtra e valida os dados
		$gump->validation_rules($res[0]);
		$gump->filter_rules($res[1]);
		$valido = $gump->run($mypost);
		$ok = $valido;
		#############################################################
    } else {
		$error .= 'Problem. Possible CSRF attack from: '.filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP).' <br/>';
		$msg->error($error);
		$log->addError('Route: '.urlRoute().' Possible CSRF attack from: '.filter_var($_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP).'  DATA:'.serialize($_POST));
    }
	$res =  (!($ok) || !empty($error));
	if ($res){
		$user = new Auth(); # Instancia o objeto
		$r = $user->register($mypost['nome'],$mypost['email']); #Registra o usuario
		if ($r){
			$msg->success('Registrado com sucesso! Consulte seu email para fazer login! ');
			$log->addSuccess('Route: '.urlRoute().' Date:'.date('Y-M-d') .'  Registered!  DATA:'.serialize($_POST));
			$app->redirect('/');
		} else {
			$error .= $gump->get_readable_errors(true);
			$msg->error('Problemas no registro!');
			$data = array(
				'atualpag' 	=> urlRoute(), 
		        'level'     => urlLevel(), 
				'mens' 		=> groupMens($msg)
			);
			$app->redirect('/login');
		}
	} else {
		$error .= $gump->get_readable_errors(true);
		$data = array(
			'atualpag' 	=> urlRoute(), 
	        'level'     => urlLevel(), 
			'mens' 		=> groupMens($msg)
		);
		$app->redirect('/login');
	}
});

$app->route('GET /forgetpass', function() use ($twig){
    echo $twig->render('forgetpass.html', array('text' => 'Login in ...'));
});
$app->route('POST /forgetpass', function() use ($twig){
    
});
$app->route('GET /verifemail', function() use ($twig){
    echo $twig->render('verifemail.html', array('text' => 'Login in ...'));
});
$app->route('POST /verifemail', function() use ($twig){
    
});
