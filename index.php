<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/funciones.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Diverpoool Mascotas | Cuidado y bienestar</title>
<meta name="description" content="Agenda peluquería canina, terapias, veterinaria, consultas y guardería para su mascota.">
<link rel="stylesheet" href="public/css/style.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
    <a class="brand" href="index.php" aria-label="Diverpoool Mascotas">
      <span class="brand-mark">D</span>
      <span><strong>Diverpoool</strong><small>MASCOTAS</small></span>
    </a>
    <button class="menu-toggle" id="menuToggle" aria-label="Abrir menú">☰</button>
    <nav id="mainNav">
      <a class="active" href="index.php">Inicio</a>
      <a href="#servicios">Servicios</a>
      <a href="#como-funciona">Cómo funciona</a>
      <a href="#nosotros">Nosotros</a>
      <a href="#contacto">Contacto</a>
      <a class="login-link" href="login.php">Iniciar sesión</a>
      <a class="btn btn-primary btn-small" href="registro.php">Registrarse</a>
    </nav>
  </div>
</header>

<main>
<section class="hero">
  <div class="container hero-grid">
    <div class="hero-copy">
      <span class="eyebrow">🐾 Bienestar para quienes hacen feliz su vida</span>
      <h1>Todo el cuidado que su mascota necesita, <span>en un solo lugar.</span></h1>
      <p>Encuentre y agende servicios para su mascota de forma sencilla: peluquería canina, terapias, veterinaria, consultas y guardería.</p>
      <div class="hero-actions">
        <a class="btn btn-primary" href="registro.php">Agendar un servicio <span>→</span></a>
        <a class="btn btn-outline" href="#servicios">Conocer servicios</a>
      </div>
      <div class="trust-row">
        <div><b>✓</b><span>Profesionales<br>especializados</span></div>
        <div><b>♡</b><span>Atención<br>personalizada</span></div>
        <div><b>✦</b><span>Espacios seguros<br>y confiables</span></div>
      </div>
    </div>
    <div class="hero-art">
      <div class="blob"></div>
      <div class="pet-card dog"><div class="pet-emoji">🐶</div><strong>Peluditos felices</strong><small>Cuidado con cariño</small></div>
      <div class="pet-card cat"><div class="pet-emoji">🐱</div><strong>Familias tranquilas</strong><small>Todo en un solo lugar</small></div>
      <div class="hero-note">Pequeñas patas,<br><b>grandes historias.</b> ♡</div>
    </div>
  </div>
</section>

<section class="services section" id="servicios">
  <div class="container">
    <div class="section-heading"><div><span class="eyebrow">NUESTROS SERVICIOS</span><h2>Todo lo que su mascota necesita</h2></div><a href="pages/servicios.php">Ver todos →</a></div>
    <div class="service-grid">
      <?php
      $services = [
        ['✂','Peluquería canina','Baño, corte, cepillado y cuidado estético.','peluqueria'],
        ['♥','Terapias','Fisioterapia, rehabilitación y terapias especializadas.','terapias'],
        ['+','Veterinaria','Consultas, vacunación, desparasitación y más.','veterinaria'],
        ['⌂','Guardería','Un espacio seguro y divertido mientras no está.','guarderia'],
        ['♣','Guardería campestre','Espacios amplios y naturales para su bienestar.','campestre'],
        ['▣','Consultas','Valoraciones generales y asesorías especializadas.','consultas']
      ];
      foreach ($services as $s): ?>
      <article class="service-card">
        <div class="service-image service-<?php echo htmlspecialchars($s[3]); ?>"><span><?php echo $s[0]; ?></span></div>
        <div class="service-body"><h3><?php echo htmlspecialchars($s[1]); ?></h3><p><?php echo htmlspecialchars($s[2]); ?></p><a href="registro.php">Agendar</a></div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="stats"><div class="container stats-grid">
  <div><strong>+500</strong><span>Mascotas felices</span></div>
  <div><strong>+1.000</strong><span>Servicios agendados</span></div>
  <div><strong>98%</strong><span>Clientes satisfechos</span></div>
  <div><strong>+5</strong><span>Años de experiencia</span></div>
</div></section>

<section class="steps section" id="como-funciona">
 <div class="container">
  <div class="section-heading centered"><div><span class="eyebrow">¿CÓMO FUNCIONA?</span><h2>Agende en 4 simples pasos</h2></div></div>
  <div class="steps-grid">
   <div class="step"><i>1</i><b>Regístrese</b><p>Cree su cuenta en Diverpoool.</p></div>
   <div class="step"><i>2</i><b>Elija el servicio</b><p>Seleccione lo que su mascota necesita.</p></div>
   <div class="step"><i>3</i><b>Seleccione fecha y hora</b><p>Elija el momento que más le convenga.</p></div>
   <div class="step"><i>4</i><b>Confirme</b><p>Reciba la confirmación de su reserva.</p></div>
  </div>
 </div>
</section>

<section class="about section" id="nosotros"><div class="container about-card"><div><span class="eyebrow">DIVERPOOOL MASCOTAS</span><h2>Más que servicios, creamos bienestar.</h2><p>Queremos facilitarle el cuidado de su mascota conectando en un mismo lugar los servicios que necesita y haciendo más sencillo el proceso de agendamiento.</p><a class="btn btn-primary" href="#contacto">Conocer más</a></div><div class="about-pets">🐶<span>♡</span>🐱</div></div></section>

<section class="contact section" id="contacto"><div class="container contact-grid"><div><span class="eyebrow">ESTAMOS PARA AYUDARLE</span><h2>¿Tiene alguna pregunta?</h2><p>Comuníquese con nosotros y le orientaremos sobre nuestros servicios y reservas.</p></div><div class="contact-box"><div>✉ <span>contacto@diverpoool.com</span></div><div>☎ <span>+57 300 000 0000</span></div><div>⌖ <span>Bogotá, Colombia</span></div></div></div></section>
</main>

<footer class="footer"><div class="container footer-grid"><div class="footer-brand"><div class="brand"><span class="brand-mark">D</span><span><strong>Diverpoool</strong><small>MASCOTAS</small></span></div><p>Bienestar para quienes hacen feliz su vida.</p></div><div><h4>Enlaces</h4><a href="index.php">Inicio</a><a href="#servicios">Servicios</a><a href="#nosotros">Nosotros</a></div><div><h4>Ayuda</h4><a href="#contacto">Contacto</a><a href="#como-funciona">Cómo funciona</a></div><div><h4>Cuenta</h4><a href="login.php">Iniciar sesión</a><a href="registro.php">Registrarse</a></div></div><div class="container footer-bottom">© <?php echo date('Y'); ?> Diverpoool Mascotas. Todos los derechos reservados.</div></footer>
<script src="public/js/main.js"></script>
</body></html>
