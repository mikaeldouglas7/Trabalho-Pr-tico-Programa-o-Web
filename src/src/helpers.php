<?php
// src/helpers.php
function ensure_logged_in(){
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])){
        header('Location: /sprint_manager/public/login.php');
        exit;
    }
}

function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

function format_seconds($secs){
    $h = floor($secs/3600);
    $m = floor(($secs%3600)/60);
    $s = $secs%60;
    return sprintf('%02d:%02d:%02d', $h, $m, $s);
}
