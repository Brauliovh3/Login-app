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
        .login-box { max-width: 420px; margin: 6vh auto; }
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
            </form>
            <hr>
            <h5 class="mt-3">Registro de Nuevo Usuario</h5>
            <form id="registerForm">
                <div class="mb-2">
                    <label class="form-label">Nombre completo</label>
                    <input type="text" id="reg_nombre" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Username</label>
                    <input type="text" id="reg_username" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Email</label>
                    <input type="email" id="reg_email" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Contraseña</label>
                    <input type="password" id="reg_password" class="form-control" required>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="button" id="btnRegister" class="btn btn-outline-success">Registrar (espera aprobación)</button>
                </div>
            </form>
            <script>
                document.getElementById('btnRegister').addEventListener('click', async function() {
                    const nombre = document.getElementById('reg_nombre').value.trim();
                    const username = document.getElementById('reg_username').value.trim();
                    const email = document.getElementById('reg_email').value.trim();
                    const password = document.getElementById('reg_password').value;

                    if (!nombre || !username || !email || !password) {
                        alert('Por favor complete todos los campos para registrarse');
                        return;
                    }

                    try {
                        const formData = new FormData();
                        formData.append('nombre', nombre);
                        formData.append('username', username);
                        formData.append('email', email);
                        formData.append('password', password);

                        const resp = await fetch(window.location.pathname + '?api=register', {
                            method: 'POST',
                            body: formData
                        });

                        const result = await resp.json();
                        if (result.success) {
                            alert(result.message);
                            // Limpiar campos
                            document.getElementById('registerForm').reset();
                        } else {
                            alert('Error: ' + (result.message || 'Error desconocido'));
                        }
                    } catch (err) {
                        console.error('Error al registrar:', err);
                        alert('No se pudo registrar. Intenta más tarde.');
                    }
                });
            </script>
        </div>
    </div>
</body>
</html>