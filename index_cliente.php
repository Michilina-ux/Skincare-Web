<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Gloow</title>
    <link rel="stylesheet" href="css/estilos.css">
    <link rel="icon" href="imagenes/Logo (1).png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <!--Iconos--><script src="https://kit.fontawesome.com/4b5e1ba30c.js" crossorigin="anonymous"></script>
   

</head>
<body>

  <header>
        <div class="inicio">
            <img src="imagenes/Logo (1).png" alt="logo " class="logo">
            <nav>
                <a class="opciones" href="inicio.html">Inició</a>
                <a class="opciones" href="productos.php">Productos</a>
                <a class="opciones" href="nuevo.html">Información</a>
                <a class="opciones" href="logout.php">Cerrar sesión</a>
            </nav>
        </div>
  </header>

<div class="carrusel">
  
    <div id="carouselExampleFade" class="carousel slide carousel-fade">
  <div class="carousel-inner">
    <div class="carousel-item active">
      <img src="imagenes/anu_1.png" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="imagenes/anu_2.png" class="d-block w-100" alt="...">
    </div>
    <div class="carousel-item">
      <img src="imagenes/anu_3.png" class="d-block w-100" alt="...">
    </div>
  </div>
  <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Previous</span>
  </button>
  <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleFade" data-bs-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="visually-hidden">Next</span>
  </button>
 </div>
</div>
</div>


<h1 class="titulo">_</h1>

<div class="tarjetas">

    <style>
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: rgb(235, 243, 197);
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #3d7a4d;
            font-size: 2.5em;
            margin-bottom: 50px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            perspective: 1000px;
        }

        .card-container {
            height: 380px;
            position: relative;
        }

        .card {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.6s ease;
            cursor: pointer;
        }

        .card-container:hover .card {
            transform: rotateY(180deg);
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 5px;
            overflow: hidden;
        }

        .card-front {
            background: linear-gradient(135deg, #95e475 0%, #c9f39a 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(173, 209, 143, 0.973);
        }

        .product-image {
            width: 150px;
            height: 200px;
           
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .product-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .product-name {
            color: white;
            font-size: 1.3em;
            font-weight: bold;
            text-align: center;
            margin-bottom: 8px;
        }

        .product-category {
            color: rgba(255,255,255,0.9);
            font-size: 0.9em;
            text-align: center;
        }

        .card-back {
            background: linear-gradient(135deg, #60a56c 0%, #b0ec87 100%);
            transform: rotateY(180deg);
            padding: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .back-title {
            color: white;
            font-size: 1.4em;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
        }

        .back-description {
            color: white;
            font-size: 0.95em;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .back-features {
            color: white;
            font-size: 0.9em;
            margin-bottom: 15px;
        }

        .back-features li {
            margin-left: 20px;
            margin-bottom: 8px;
        }

        .back-price {
            color: white;
            font-size: 1.5em;
            font-weight: bold;
            text-align: center;
            margin-top: auto;
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 2em;
            }

            .products-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
            }

            .card-container {
                height: 350px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Destacados</h1>
        
        <div class="products-grid">
            <!-- Tarjeta 1 -->
            <div class="card-container">
                <div class="card">
                    <div class="card-face card-front">
                        <div class="product-image">
                            <img src="imagenes/pro_1.png" alt="Serum Retinol">
                        </div>
                        <div class="product-name">Serum Retinol</div>
                        <div class="product-category">anti-aging</div>
                    </div>
                    <div class="card-face card-back">
                        <div class="back-title">Serum Retinol</div>
                        <div class="back-description">
                            Potente serum anti-edad con retinol puro que ayuda a reducir líneas de expresión y arrugas.
                        </div>
                        <ul class="back-features">
                            <li>Retinol al 0.5%</li>
                            <li>Reduce arrugas</li>
                            <li>Mejora textura</li>
                        </ul>
                        <div class="back-price">$450</div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 2 -->
            <div class="card-container">
                <div class="card">
                    <div class="card-face card-front">
                        <div class="product-image">
                            <img src="imagenes/pro_3.png" alt="Tonico Facial">
                        </div>
                        <div class="product-name">Tonico Facial</div>
                        <div class="product-category">limpieza</div>
                    </div>
                    <div class="card-face card-back">
                        <div class="back-title">Tonico Facial</div>
                        <div class="back-description">
                            Tónico purificante que equilibra el pH de tu piel y minimiza los poros.
                        </div>
                        <ul class="back-features">
                            <li>Sin alcohol</li>
                            <li>Minimiza poros</li>
                            <li>Hidratación ligera</li>
                        </ul>
                        <div class="back-price">$280</div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 3 -->
            <div class="card-container">
                <div class="card">
                    <div class="card-face card-front">
                        <div class="product-image">
                            <img src="imagenes/pro_5.png" alt="Crema Hidratante">
                        </div>
                        <div class="product-name">Crema Hidratante</div>
                        <div class="product-category">hidratación</div>
                    </div>
                    <div class="card-face card-back">
                        <div class="back-title">Crema Hidratante</div>
                        <div class="back-description">
                            Crema nutritiva de rápida absorción con ácido hialurónico y vitaminas.
                        </div>
                        <ul class="back-features">
                            <li>Ácido hialurónico</li>
                            <li>24h de hidratación</li>
                            <li>Piel suave</li>
                        </ul>
                        <div class="back-price">$380</div>
                    </div>
                </div>
            </div>

            <!-- Tarjeta 4 -->
            <div class="card-container">
                <div class="card">
                    <div class="card-face card-front">
                        <div class="product-image">
                            <img src="imagenes/pro_4.png" alt="Mascarilla Hidratante">
                        </div>
                        <div class="product-name">Mascarilla Hidratante</div>
                        <div class="product-category">mascarillas</div>
                    </div>
                    <div class="card-face card-back">
                        <div class="back-title">Mascarilla Hidratante</div>
                        <div class="back-description">
                            Mascarilla intensiva que restaura la hidratación profunda en solo 15 minutos.
                        </div>
                        <ul class="back-features">
                            <li>Efecto inmediato</li>
                            <li>Piel luminosa</li>
                            <li>Hidratación intensa</li>
                        </ul>
                        <div class="back-price">$199</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
 

<div>
    <img src="imagenes/post_1.png" class="img-fluid" alt="...">
</div>


<!--Footer-->
<footer class="pie-pagina">
    <div class="grupo-1">
        <div class="box">
            <figure>
                <a href="#"> 
                <img src="imagenes/Logo (1).png" alt="logo de mich" class="logo">
                </a>
            </figure>
        </div>
        <div class="box">
            <h2>CONOCENOS</h2>
            <p>"Descubre el secreto de una piel radiante".</p>
        <p>¡Bienvenido a la familia! Estás a punto de descubrir por qué miles de mujeres han transformado su rutina de skincare con nosotros. Prepárate para enamorarte de tu piel.</p>
         </div>
        <div class="box">
            <h2>SIGUENOS</h2>
            <div class="red-social">
                <a href="https://www.facebook.com/profile.php?id=61577896257138" class="fa fa-facebook"></a>
                <a href="https://www.instagram.com/mich_donas_?igsh=MXk4NWZ6eDZyZDdw" target="blank" class="fa fa-instagram"></a>
                <a href="#" class="fa fa-twitter"></a>
            </div>
        </div>
    </div>

  <div class="grupo-2">
  <small>&copy; 2025 <b>New Gloow</b> - Todos los Derechos Reservados.</small>
  </div>
</footer>  


</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
</html>