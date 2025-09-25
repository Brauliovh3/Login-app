<?php
// Test directo de la API dashboard-stats
header('Content-Type: application/json');

// Simular una sesión de administrador para probar
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'administrador';
$_SESSION['username'] = 'admin';

// Llamar al dashboard con la API
$_GET['api'] = 'dashboard-stats';

// Incluir el dashboard para ejecutar la API
include 'dashboard.php';
?>