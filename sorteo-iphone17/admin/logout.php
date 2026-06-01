<?php
/**
 * ============================================
 * ADMIN - LOGOUT
 * Cierra la sesión y redirige al login
 * ============================================
 */
session_start();
session_destroy();
header('Location: login.php');
exit;
