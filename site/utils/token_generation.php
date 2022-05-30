<?php

function generate_token($name) {
    if (!isset($_SESSION[strtoupper($name)])) {
        $post_comment_token = bin2hex(openssl_random_pseudo_bytes(64));
        $_SESSION[strtoupper($name)] = $post_comment_token;
        return $post_comment_token;
    } else {
        return $_SESSION[strtoupper($name)];
    }
}