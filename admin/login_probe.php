<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'admin';
include __DIR__ . '/index.php';
