<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Café Eliel | Estudia, respira y crea</title>
    <meta
      name="description"
      content="Una landing moderna de Café Eliel para universitarios que buscan un espacio cálido para estudiar, trabajar y desconectar."
    />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="styles.css" />
  </head>
  <body>
    <header class="site-header">
      <div class="container nav-bar">
        <a href="#hero" class="brand">Café Eliel</a>
        <nav class="nav-links" aria-label="Navegación principal">
          <a href="#hero">Inicio</a>
          <a href="#beneficios">Beneficios</a>
          <a href="#promocion">Promoción</a>
          <a href="#contacto">Contacto</a>
        </nav>
        <div class="nav-actions">
          <span class="welcome-pill">Hola, <?= getDisplayName(); ?></span>
          <?php if (isLoggedIn()): ?>
            <?php if (isAdmin()): ?>
              <a href="dashboard.php" class="btn btn-small btn-secondary">Dashboard</a>
            <?php endif; ?>
            <a href="logout.php" class="btn btn-small btn-secondary">Salir</a>
          <?php else: ?>
            <a href="login.php" class="btn btn-small btn-primary">Iniciar sesión</a>
          <?php endif; ?>
        </div>
      </div>
    </header>

    <main>
      <section id="hero" class="hero-section">
        <div class="container hero-grid">
          <div class="hero-copy">
            <p class="eyebrow">Espacio ideal para jóvenes creativos</p>
            <h1>Tu dosis de foco y calma.</h1>
            <p class="hero-text">
              Un café pensado para estudiar, trabajar y compartir buenos momentos entre clases.
            </p>
            <div class="hero-actions">
              <a href="#contacto" class="btn btn-primary">Visítanos hoy</a>
              <a href="#beneficios" class="btn btn-secondary">Ver beneficios</a>
            </div>
          </div>
          <div class="hero-media">
            <img
              src="https://images.unsplash.com/photo-1511920170033-f8396924c348?auto=format&fit=crop&w=1200&q=80"
              alt="Café Eliel con ambiente acogedor para estudiar"
            />
          </div>
        </div>
      </section>

      <section id="beneficios" class="section">
        <div class="container">
          <div class="section-heading">
            <p class="eyebrow">Por qué nos eligen</p>
            <h2>Todo lo esencial, bien hecho.</h2>
          </div>
          <div class="benefits-grid">
            <article class="card">
              <h3>Ambiente estudiantil</h3>
              <p>Mesas cómodas, luz natural y un ritmo perfecto para leer, trabajar o planear tu semana.</p>
            </article>
            <article class="card">
              <h3>Sabores que acompañan</h3>
              <p>Desde cafés especiales hasta bebidas frías y snacks que te energizan sin complicarte.</p>
            </article>
            <article class="card">
              <h3>Momento de conexión</h3>
              <p>Un lugar cálido para encontrarte con amigos, hacer networking o simplemente respirar.</p>
            </article>
          </div>
        </div>
      </section>

      <section id="promocion" class="section promo-section">
        <div class="container promo-card">
          <div>
            <p class="eyebrow">Promoción de la semana</p>
            <h2>Compra una bebida y suma un descuento especial.</h2>
            <p>
              Lleva tu rutina al siguiente nivel con una propuesta pensada para tus horas de estudio y tus pausas favoritas.
            </p>
          </div>
          <a href="https://www.instagram.com/" class="btn btn-primary" target="_blank" rel="noopener noreferrer">
            Quiero mi bebida
          </a>
        </div>
      </section>

      <section id="contacto" class="section">
        <div class="container contact-card">
          <div>
            <p class="eyebrow">Contacto</p>
            <h2>Nos encantaría verte por aquí.</h2>
            <p>Estamos en la zona universitaria y abrimos temprano para que empieces tu día con energía.</p>
          </div>
          <div class="contact-details">
            <p><strong>Dirección:</strong> Av. Siempre Viva 123</p>
            <p><strong>Horario:</strong> Lunes a Sábado · 7:00 - 22:00</p>
            <p><strong>Teléfono:</strong> +57 300 123 4567</p>
          </div>
        </div>
      </section>
    </main>
  </body>
</html>
