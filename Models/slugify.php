<?php
// Devuelve un slug URL amigable a partir de un string
function slugify($text) {
    // Reemplaza espacios y caracteres especiales
    $text = preg_replace('~[áàäâã]~u', 'a', $text);
    $text = preg_replace('~[éèëê]~u', 'e', $text);
    $text = preg_replace('~[íìïî]~u', 'i', $text);
    $text = preg_replace('~[óòöôõ]~u', 'o', $text);
    $text = preg_replace('~[úùüû]~u', 'u', $text);
    $text = preg_replace('~[ñ]~u', 'n', $text);
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text;
}
