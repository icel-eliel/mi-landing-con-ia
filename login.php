<?php require_once __DIR__ . '/config.php';

if (isLoggedIn()) {
    $redirect = isAdmin() ? 'dashboard.php' : 'index.php';
    header('Location: ' . $redirect);
    exit;
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errorMessage = 'Ingresa tu correo y contraseña.';
    } else {
        try {
            $pdo = getDbConnection();
            $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, role, password_hash FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && verifyPassword($password, $user['password_hash'])) {
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role'] = $user['role'];

                $redirect = $user['role'] === 'admin' ? 'dashboard.php' : 'index.php';
                header('Location: ' . $redirect);
                exit;
            }

            $errorMessage = 'Correo o contraseña incorrectos.';
        } catch (PDOException $e) {
            $errorMessage = 'No fue posible conectar con la base de datos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Iniciar sesión | Café Eliel</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <main class="auth-page">
      <section class="auth-card">
        <p class="eyebrow">Acceso</p>
        <h1>Inicia sesión</h1>
        <p class="hero-text">Ingresa tus credenciales para entrar a la experiencia para usuarios o al dashboard de administración.</p>
        <p class="hero-text">Demo inicial: admin@cafeeliel.com / admin123</p>

        <?php if ($errorMessage !== ''): ?>
          <div class="alert alert-error"><?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" class="auth-form">
          <label>
            Correo electrónico
            <input type="email" name="email" required />
          </label>
          <label>
            Contraseña
            <input type="password" name="password" required />
          </label>
          <button type="submit" class="btn btn-primary">Entrar</button>
        </form>

        <p class="helper-text">
          <a href="index.php">Volver a la landing</a>
        </p>
      </section>
    </main>
  </body>
</html>
