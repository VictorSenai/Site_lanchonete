  
<!-- cabeçalho -->
<header id="#cabecalho" class="header">
    <div>
        <!-- titulo -->
    <h1>Lanchonete Do Burgas</h1>
    </div>
    

    <div>
        <!-- Navegação -->
    <nav class="nav">
        <ul>
            <li><a href="index.php#cabecalho">Home</a></li>
            <li><a href="index.php#produtos">Ver Cardápio</a><li>
            <li> <a href="carrinho.php">Carrinho </a></li>
            <li><a href="sobre.php">sobre</a></li>
        </ul>
    </nav>
    </div>
     
    <div class="div-texto">
        <!-- conta -->
        <a> Conta </a>
    </div>
    
</header>

<style>

/* ===== HEADER ===== */
.header {
    width: 100%;
    height: 150px; /* altura fixa */
    background: linear-gradient(135deg, #e44d26 0%, #c23b1f 100%);
    padding: 0 30px;
    display: flex;
    align-items: center;
    justify-content:left;
    color: white;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    top: 0;
}

/* título */
.header h1 {
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    margin-left:100px;
}

/* ===== NAV CONTAINER ===== */
.nav {
    background: white; /* quadrado branco */
    padding: 10px 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
      margin-left:250px;
}

/* lista da nav */
.nav ul {
    list-style: none;
    display: flex;
    gap: 20px;
    margin: 0;
    padding: 0;
}

.nav ul li {
    display: inline-block;
}

/* links da nav */
.nav ul li a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    padding: 6px 10px;
    position: relative;
    transition: all 0.3s ease;
    border-radius: 6px;
}

/* efeito hover: vermelho e linha */
.nav ul li a:hover {
    color: #e44d26;
}

.nav ul li a::after {
    content: "";
    position: absolute;
    bottom: -3px;
    left: 0;
    width: 0;
    height: 2px;
    background: #e44d26;
    transition: width 0.3s ease;
}

.nav ul li a:hover::after {
    width: 100%;
}

/* ===== CONTA ===== */
.div-texto a {
    color: white;
    font-weight: 600;
    text-decoration: none;
    border: 2px solid white;
    padding: 6px 14px;
    border-radius: 8px;
    transition: all 0.3s ease;
    margin-left: 20px; /* aproxima lateralmente */
    margin-left:600px;
}

.div-texto a:hover {
    background: white;
    color: #c23b1f;
}

/* ===== RESPONSIVIDADE ===== */
@media (max-width: 768px) {
    .header {
        height: auto;
        flex-direction: column;
        align-items: center;
        padding: 15px;
    }

    .nav {
        margin-top: 10px;
    }

    .nav ul {
        flex-direction: column;
        gap: 10px;
    }

    .div-texto {
        margin-top: 10px;
    }
}


</style>