<?php
session_start();

// Si ya está autenticado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Config DB
$host = 'localhost';
$dbname = 'login_app';
$username = 'root';
$password = '';

$errors = [];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    $errors[] = 'Error de conexión a la base de datos.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userInput = trim($_POST['user'] ?? '');
    $passInput = $_POST['password'] ?? '';

    if ($userInput === '' || $passInput === '') {
        $errors[] = 'Usuario y contraseña son requeridos.';
    }

    if (empty($errors)) {
        // Buscar por usuario o email
        $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE (usuario = :u OR email = :u) AND deleted_at IS NULL LIMIT 1');
        $stmt->execute([':u' => $userInput]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Si la contraseña está hasheada con password_hash
            if (!empty($user['password']) && (password_verify($passInput, $user['password']) || $user['password'] === $passInput)) {
                // Autenticación exitosa
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['nombre'];
                header('Location: dashboard.php');
                exit();
            } else {
                $errors[] = 'Credenciales inválidas.';
            }
        } else {
            $errors[] = 'Usuario no encontrado.';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - DRTC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .login-box { max-width: 800px; margin: 6vh auto; width: 100%; }
        .card { border-radius: 10px; box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
        .brand { font-weight: 700; color: #ff8c00; }
    </style>
</head>
<body>
    <div class="login-box">
        <div class="card p-4">
            <h3 class="brand mb-3">DRTC Apurímac</h3>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="mb-3">
                    <label class="form-label">Usuario o Email</label>
                    <input type="text" name="user" class="form-control" value="<?php echo isset($userInput) ? htmlspecialchars($userInput) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <button class="btn btn-primary">Ingresar</button>
                    <a href="/" class="text-muted">Volver</a>
                </div>
                <div class="mt-3 text-center">
                    <p class="mb-2">¿No tienes cuenta?</p>
                    <a href="/register" class="fw-bold">Crear cuenta nueva</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>