<?php

function validate_field($field, $max_length) {
    return isset($field) && strlen($field) > 0 && strlen($field) <= $max_length;
}