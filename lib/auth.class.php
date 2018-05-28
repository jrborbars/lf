<?php
#######################################################################
# CLASSES
#######################################################################

//namespace Auth;

class Auth{
    private $u;
    private $r;
    public function __construct($u,$r){
      $this->user = $$u;
      $this->role = $$r;
    }
    public function registerUser($uname,$uemail){
		$tbl = 'usuarios';
		$hashopt = ['cost' => 5 ]; // default = 10
		$ehash = password_hash($uemail, PASSWORD_DEFAULT,$hashopt);		# Para ficar profissional, precisa verificar a data de expiracão do hash
		$ativo = false;		# usuário não pode estar ativo se não confirmou o email
		$error = '';
		$senha = '';
		# tenta inserir
		try {
			require_once('email.func.php');
			$dado = Model::factory($tbl)->create();
			$log->addNotice('Route: '.urlRoute().'[Usuario]: data: '.serialize($email));
			$i=0;
			foreach($mypost as $campo){
				// não atribui ao id e email_hash
				$dado->{$campo[$i]} = $mypost[$i];
				$i++;
			}
			$ok = $dado->save();
			$enviado = envia_email($uname,$uemail,$ehash);
			if(!$enviado) {
				$error .= 'A mensagem não pôde ser enviada.';
			}
		} catch (PDOException $err){
			# entra aqui se houver exceção PDO
			$error .= $err->getMessage();
		} catch (Throwable $t) {
			// Executed only in PHP 7, will not match in PHP 5.x
			# entra aqui se houver exceção geral
			$error .= $t->getMessage();
		} catch (Exception $e){
			// Executed only in PHP 5.x, will not be reached in PHP 7
			# entra aqui se houver exceção geral
			$error .= $e->getMessage();
		} finally {
			# finally SEMPRE é executado
			$result = (!($ok) || !empty($error));
		}
		return $result;
    }

    public function login($umail,$upass){
		$error = '';
		$log->addInfo('Route: '.urlRoute());
		$ok=null;
		# validate data
		$senha = $mypost['senha'];
		$email = $mypost['usuario'];
		try{
			$log->addNotice('Route: '.urlRoute().'[Usuario]: data: '.serialize($email));
			$usuario = ORM::forTable('usuarios')->where('email', $email)->where('ativo', true)->findOne();
			$verif = password_verify($senha,$usuario->senha);
			if ($verif){
				$phash = crypt($usuarios->nome, $usuarios->email);
				$_SESSION['login'] = $phash;
				$_SESSION['username'] = $usuarios->nome;
				$_SESSION['comm'] = sha256($usuarios->email);
				$_SESSION['comm']['raw'] = $usuarios->email;
				$_SESSION['inicio'] = time();	// carimbo de entrada no sistema
				$_SESSION['vida'] = 600; // 60s = 1min ==> tempo de permanência
			} else {
				$error .= $gump->get_readable_errors(true);
				$msg->error('Problemas no do login. Dados inválidos. '.$error);
				$app->redirect('/login');
			}
		} catch (Throwable $t) {
			$error = $t->getMessage();
		} catch (Exception $e){
			$error = $e->getMessage();
		}
		$result = (!($ok) || !empty($error));
		return $result;
	}
############################################################################################################# 

   public function is_loggedin(){
      if(	isset($_SESSION['comm']) &&
    		isset($_SESSION['comm']['raw']) &&
    		$_SESSION['comm'] == sha256($_SESSION['comm']['raw']) &&
    		$_SESSION['login'] == crypt($_SESSION['username'],$_SESSION['comm']['raw'])){
    			
         return true;
      }
   }
    public function user_role(){
         return $_SESSION['username']['role'];
   }
   
    public function check_logRole($u, $r=9){	// check if "logged in" and role ($u = object, $r = role)
    	if (!$u->is_loggedin()){
			$msg->error('Voce nao esta logado ou o tempo de sua sessao expirou. '.$error);
			throw new Exception("You are not logged in. Maybe inactivity? "); 
		}
		if ( $u->user_role() > $r ){
			$msg->error('Voce nao possui permissao para ver esta pagina. O administrador sera comunicado. '.$error);
			throw new Exception("You are not able to view this page. Administrator was comunicated. "); 
		}
   }
/*   
    public function redirect($url){
       header("Location: $url");
   }
 */
   public function logout(){
   		$error=null;
		try {
			unset($_SESSION['login']);
	        unset($_SESSION['comm']);
   	        unset($_SESSION['comm']['raw']);
	        session_destroy();
		} catch (Throwable $t) {
			$error = $t->getMessage();
		} catch (Exception $e){
			// Executed only in PHP 5.x, will not be reached in PHP 7
			# entra aqui se houver exceção geral
			$error = $e->getMessage();
		} finally {
			if ($error){
				return false;
			} else {
	    		return true;
			}
		}
	
   }
} // EOC