<?php 

function incorrect_field($field) {
    return !isset($field);
}

function validate_array($arr) {
    if (count(array_filter($arr, 'incorrect_field'))) {
        die("At least one field from an array was null when it shouldn't have been.");
    }
    return true;
}

?>