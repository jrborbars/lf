<?php
// Conecta em outro banco de dados
class Columns extends Model {
    public static $_connection_name = 'alternate';
}

// load new MODELS automatically
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator('./models')) as $filename){
        if (substr($filename,9,5) == 'model' && substr($filename,9) != 'model.php'){
			myautoload($filename);
		}
}

#include_once('model_usuarios.php');

/*
// validation inside models
abstract class AbstractModel extends Model {
    public function save() {
        if($this->is_valid()) {
            $fields = $this->as_array();
            unset($fields['field_to_exclude']); // this removes the field you do not desire to be passed to the DB
            $this->hydrate($fields); // re-populate the model
            parent::save();
        }
    }
    protected function is_valid() {
        // perform your validation here
    }
}

class User extends AbstractModel {}
*/