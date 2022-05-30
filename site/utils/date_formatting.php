<?php 

function format_date($date) {
    $date_to_format = new DateTime($date, new DateTimeZone("Europe/Copenhagen"));
    return $date_to_format->format("d/m/Y");
}

function format_date_with_time($date) {
    $date_to_format = new DateTime($date, new DateTimeZone("Europe/Copenhagen"));
    return $date_to_format->format("d/m/Y") . ", at " . $date_to_format->format("H:i");
}

function today() {
    $date_to_format = new DateTime("now", new DateTimeZone("Europe/Copenhagen"));
    return $date_to_format->format("Y-m-d H:i:s");
}