<?php
//$filter_factory = new Aura\Filter\FilterFactory();
//$filter = $filter_factory->newValueFilter();

$app->route('/', function() use ($twig, $log, $msg,$appname){
	$log->addInfo('Route: '.urlRoute());
	$menu = array(
		'Home' 			=> 'home',
		'Cadastros' 	=> 'cadastros',
		'Usuários' 	    => 'admin/usuarios',
		'Sobre' 		=> 'sobre',
		'Contato' 		=> 'contato',
		'Logout' 		=> 'logout');
	$data = array(
			'appname'	=> $appname,
	        'level'     => urlLevel(),
			'atualpag' 	=> urlroute(),
			'menu' 		=> $menu,
			'mens' 		=> groupMens($msg)
			);
	echo $twig->render('index.html', $data);
});

$app->route('/sobre', function() use ($twig, $log, $msg){
	$log->addInfo('Route: '.urlRoute());
	$menu = array(
		'Home' 			=> 'home',
		'Cadastros' 	=> 'cadastros',
		'Usuários' 	    => 'usuarios',
		'Sobre' 		=> 'sobre',
		'Contato' 		=> 'contato',
		'Logout' 		=> 'logout');
	$data = array(
	        'level'     => urlLevel(),
			'atualpag' 	=> urlroute(),
			'menu' 		=> $menu,
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render('sobre.html', $data);
});


$app->route('GET /contato', function() use ($twig, $log, $msg){
	$log->addInfo('Route: '.urlRoute());
	$menu = array(
		'Home' 			=> 'home',
		'Cadastros' 	=> 'cadastros',
		'Usuários' 	    => 'usuarios',
		'Sobre' 		=> 'sobre',
		'Contato' 		=> 'contato',
		'Logout' 		=> 'logout');
	$data = array(
	        'level'     => urlLevel(),
			'atualpag' 	=> urlroute(),
			'menu' 		=> $menu,
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render('contato.html', $data);
});

$app->route('POST /contato', function() use ($twig, $log, $msg){
	$log->addInfo('Route: '.urlRoute());
	$menu = array(
		'Home' 			=> 'home',
		'Cadastros' 	=> 'cadastros',
		'Usuários' 	    => 'usuarios',
		'Sobre' 		=> 'sobre',
		'Contato' 		=> 'contato',
		'Logout' 		=> 'logout');
	$data = array(
	        'level'     => urlLevel(),
			'atualpag' 	=> urlroute(),
			'menu' 		=> $menu,
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render('contato.html', $data);
});


$app->route('GET /dashboard/blocos', function() use ($twig, $log, $msg){
	$log->addInfo('Route: '.urlRoute());
	$menu = array(
		'Home' 			=> 'home',
		'Cadastros' 	=> 'cadastros',
		'Usuários' 	    => 'usuarios',
		'Sobre' 		=> 'sobre',
		'Contato' 		=> 'contato',
		'Logout' 		=> 'logout');
	$data = array(
	        'level'     => urlLevel(),
			'atualpag' 	=> urlroute(),
			'menu' 		=> $menu,
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render('blocos.html', $data);
});

$app->route('GET /dashboard', function() use ($twig, $log, $msg){
	$log->addInfo('Route: '.urlRoute());
	$menu = array(
		'Home' 			=> 'home',
		'Cadastros' 	=> 'cadastros',
		'Usuários' 	    => 'usuarios',
		'Sobre' 		=> 'sobre',
		'Contato' 		=> 'contato',
		'Logout' 		=> 'logout');
	$data = array(
	        'level'     => urlLevel(),
			'atualpag' 	=> urlroute(),
			'menu' 		=> $menu,
			'mens' 		=> groupMens($msg)
			);
 echo $twig->render('dashboard.html', $data);
});

/*
//  Página não encontrada
$app->notFound(function () use ($app) {
    $data = array(
		'menu' => $menupri,
        'breadcrumbs' => $bc,
		'level' => urlLevel()
	);
    $app->render('e404.html', $data);
});
*/

include_once('route_users.php');


/*
###########################################################################################
// load new ROUTES automatically
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./routes')) as $filename){
        if (
        	$filename != './routes/.' and 
        	$filename != './routes/..' and 
        	$filename != './routes/route.php' and
        	$filename != './routes/route_auth.php'){
			echo $filename;
			myautoload($filename);
		}
}


function myautoload($class_name) {
    if(file_exists($class_name)) {
		try {
			require_once($class_name);   
		} catch (\Exception $e){
			 $message = "<pre>".$e->getMessage()."</pre>";
			 echo $message;
			 exit;
		}
    } else {
        throw new Exception('Impossível carregar'. $class_name);
    }
}
*/