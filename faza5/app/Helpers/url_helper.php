<?php

/**
 * Opis: Pomocnik za dohvatanje asseta
 * 
 * @version 1.0
 * 
 */

use App\Models\UserM;

if (!function_exists('product_banner')) {
    function product_banner($id) {
        echo base_url("uploads/product/{$id}/banner.jpg");
    }
}

if (!function_exists('product_capsule')) {
    function product_capsule($id) {
        echo base_url("uploads/product/{$id}/capsule.jpg");
    }
}

if (!function_exists('product_ss')) {
    function product_ss($id, $pos) {
        echo base_url("uploads/product/{$id}/ss{$pos}.jpg");
    }
}

if (!function_exists('product_video')) {
    function product_video($id) {
        echo base_url("uploads/product/{$id}/video.webm");
    }
}

if (!function_exists('bundle_banner')) {
    function bundle_banner($id) {
        echo base_url("uploads/bundle/{$id}/banner.jpg");
    }
}

if (!function_exists('user_avatar')) {
    function user_avatar($id) {
        echo (new UserM())->getAvatar($id);
    }
}

if (!function_exists('product_url')) {
    function product_url($controller, $id) {
        echo site_url($controller . "/product/" . $id);
    }
}

if (!function_exists('bundle_url')) {
    function bundle_url($controller, $id) {
        echo site_url($controller . "/bundle/" . $id);
    }
}

if (!function_exists('user_url')) {
    function user_url($controller, $id) {
        echo site_url($controller . "/profile/" . $id);
    }
}
