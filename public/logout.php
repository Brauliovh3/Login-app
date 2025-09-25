<?php
session_start();

// Asegurarse de limpiar toda la sesión
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>