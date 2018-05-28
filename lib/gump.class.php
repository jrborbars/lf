<?php
#######################################################################
# CLASSES
#######################################################################

class GumpValidator extends GUMP{

    /**
     * Verify that a value EXACTLY match other value (like naked passwords).
     *
     * Usage: '<index>' => 'match value'
     *
     * @param string $field
     * @param array  $input
     * @param null   $field2
     *
     * @return mixed
     */
    public function validate_match($field, $input, $field2 = NULL){
        if (!isset($input[$field])) {
            return;
        }
        $param = trim(strtolower($input[$field2]));
        $value = trim(strtolower($input[$field]));
        if ($param === $value) {
            return;
        } else {
            return array(
                'field' => $field,
                'value' => $value,
                'rule' => __FUNCTION__,
                'param' => $param,
            );
        }
    }
} // EOC
