<?php
###########################################################################################
// load new LIBS automatically
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./lib')) as $filename){
        if (substr($filename,-10) == '.cclass.php'){
			myautoload($filename);
		}
}

function myautoload($name) {
    if(file_exists($name)) {
		try {
			require_once($name);   
		} catch (\ Exception $e){
			 $message = "<pre>".$e->getMessage()." - from myautoload</pre>";
			 echo $message;
			 exit;
		}
    } else {
        throw new Exception('Impossível carregar'. $name);
    }
}