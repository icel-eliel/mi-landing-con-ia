<?php require_once __DIR__ . '/config.php';
requireAuth('admin');

$message = '';
$messageType = 'success';

try {
    $pdo = getDbConnection();
    ensureDemoData($pdo);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'add_user') {
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = $_POST['role'] ?? 'user';

            if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
                $message = 'Completa todos los campos del usuario.';
                $messageType = 'error';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, email, password_hash, role) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $role]);
                $message = 'Usuario agregado correctamente.';
            }
        } elseif ($action === 'update_user') {
            $userId = (int) ($_POST['user_id'] ?? 0);
            $firstName = trim($_POST['first_name'] ?? '');
            $lastName = trim($_POST['last_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = $_POST['role'] ?? 'user';

            if ($userId <= 0 || $firstName === '' || $lastName === '' || $email === '') {
                $message = 'Selecciona un usuario y completa los datos.';
                $messageType = 'error';
            } else {
                $fields = ['first_name' => $firstName, 'last_name' => $lastName, 'email' => $email, 'role' => $role];
                $sql = 'UPDATE users SET first_name = ?, last_name = ?, email = ?, role = ?';
                $params = [$firstName, $lastName, $email, $role];

                if ($password !== '') {
                    $sql .= ', password_hash = ?';
                    $params[] = password_hash($password, PASSWORD_DEFAULT);
                }

                $sql .= ' WHERE id = ?';
                $params[] = $userId;

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);
                $message = 'Usuario actualizado correctamente.';
            }
        }
    }

    $editUserId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
    $selectedUser = null;

    if ($editUserId > 0) {
        $stmt = $pdo->prepare('SELECT id, first_name, last_name, email, role FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$editUserId]);
        $selectedUser = $stmt->fetch();
    }

    $usersStmt = $pdo->query('SELECT id, first_name, last_name, email, role, created_at FROM users ORDER BY created_at DESC');
    $users = $usersStmt->fetchAll();

    $today = date('Y-m-d');
    $salesStmt = $pdo->prepare('SELECT COUNT(*) AS total_sales, COALESCE(SUM(total_amount), 0) AS total_amount FROM sales WHERE sale_date = ?');
    $salesStmt->execute([$today]);
    $salesSummary = $salesStmt->fetch();

    $recentSalesStmt = $pdo->prepare('SELECT customer_name, description, total_amount, created_at FROM sales WHERE sale_date = ? ORDER BY created_at DESC LIMIT 8');
    $recentSalesStmt->execute([$today]);
    $recentSales = $recentSalesStmt->fetchAll();
} catch (Throwable $e) {
    $message = 'No fue posible cargar el dashboard.';
    $messageType = 'error';
    $users = [];
    $salesSummary = ['total_sales' => 0, 'total_amount' => 0];
    $recentSales = [];
}
?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard | Café Eliel</title>
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header class="site-header">
      <div class="container nav-bar">
        <a href="index.php" class="brand">Café Eliel</a>
        <div class="nav-actions">
          <span class="welcome-pill">Hola, <?= getDisplayName(); ?></span>
          <a href="logout.php" class="btn btn-small btn-secondary">Salir</a>
        </div>
      </div>
    </header>

    <main class="dashboard-page">
      <div class="container dashboard-grid">
        <section class="dashboard-card">
          <p class="eyebrow">Panel de administración</p>
          <h1>Gestión de usuarios</h1>
          <?php if ($message !== ''): ?>
            <div class="alert alert-<?= $messageType === 'error' ? 'error' : 'success' ?>"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
          <?php endif; ?>

          <form method="post" class="stacked-form">
            <input type="hidden" name="action" value="add_user" />
            <div class="form-grid">
              <label>Nombre<input type="text" name="first_name" required /></label>
              <label>Apellido<input type="text" name="last_name" required /></label>
              <label>Correo<input type="email" name="email" required /></label>
              <label>Contraseña<input type="password" name="password" required /></label>
              <label>Rol<select name="role"><option value="user">User</option><option value="admin">Admin</option></select></label>
            </div>
            <button class="btn btn-primary" type="submit">Agregar usuario</button>
          </form>
        </section>

        <section class="dashboard-card">
          <p class="eyebrow">Actualizar usuarios</p>
          <h2>Editar datos</h2>
          <form method="post" class="stacked-form">
            <input type="hidden" name="action" value="update_user" />
            <label>Usuario
              <select name="user_id" required>
                <option value="">Selecciona un usuario</option>
                <?php foreach ($users as $user): ?>
                  <option value="<?= (int) $user['id'] ?>" <?= ($selectedUser && (int) $selectedUser['id'] === (int) $user['id']) ? 'selected' : '' ?>><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['email'] . ')', ENT_QUOTES, 'UTF-8') ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <div class="form-grid">
              <label>Nombre<input type="text" name="first_name" value="<?= htmlspecialchars($selectedUser['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required /></label>
              <label>Apellido<input type="text" name="last_name" value="<?= htmlspecialchars($selectedUser['last_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required /></label>
              <label>Correo<input type="email" name="email" value="<?= htmlspecialchars($selectedUser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required /></label>
              <label>Nueva contraseña<input type="password" name="password" /></label>
              <label>Rol<select name="role"><option value="user" <?= (($selectedUser['role'] ?? 'user') === 'user') ? 'selected' : '' ?>>User</option><option value="admin" <?= (($selectedUser['role'] ?? '') === 'admin') ? 'selected' : '' ?>>Admin</option></select></label>
            </div>
            <button class="btn btn-secondary" type="submit">Guardar cambios</button>
          </form>
        </section>

        <section class="dashboard-card sales-card">
          <p class="eyebrow">Ventas del día</p>
          <h2><?= htmlspecialchars(date('d/m/Y'), ENT_QUOTES, 'UTF-8') ?></h2>
          <div class="stats-grid">
            <div>
              <p class="stat-label">Órdenes</p>
              <p class="stat-value"><?= (int) $salesSummary['total_sales'] ?></p>
            </div>
            <div>
              <p class="stat-label">Recaudación</p>
              <p class="stat-value">$<?= number_format((float) $salesSummary['total_amount'], 2, ',', '.') ?></p>
            </div>
          </div>

          <h3>Últimas ventas</h3>
          <?php if ($recentSales): ?>
            <ul class="sales-list">
              <?php foreach ($recentSales as $sale): ?>
                <li>
                  <strong><?= htmlspecialchars($sale['customer_name'] ?? 'Cliente', ENT_QUOTES, 'UTF-8') ?></strong>
                  <span><?= htmlspecialchars($sale['description'], ENT_QUOTES, 'UTF-8') ?></span>
                  <span>$<?= number_format((float) $sale['total_amount'], 2, ',', '.') ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="hero-text">Aún no hay ventas registradas hoy.</p>
          <?php endif; ?>
        </section>

        <section class="dashboard-card users-list-card">
          <p class="eyebrow">Usuarios registrados</p>
          <h2>Administrar accesos</h2>
          <ul class="users-list">
            <?php foreach ($users as $user): ?>
              <li>
                <div>
                  <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8') ?></strong>
                  <p><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?> · <?= htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
                <a href="dashboard.php?edit=<?= (int) $user['id'] ?>" class="btn btn-small btn-secondary">Editar</a>
              </li>
            <?php endforeach; ?>
          </ul>
        </section>
      </div>
    </main>
  </body>
</html>
