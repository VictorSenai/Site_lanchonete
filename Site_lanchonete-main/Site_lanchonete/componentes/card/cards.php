<?php


// Carregar produtos do JSON
$produtos = json_decode(file_get_contents("produtos.json"), true);
if (!$produtos) $produtos = [];

// Mensagens
$mensagem = $_SESSION['mensagem'] ?? '';
$erro = $_SESSION['erro'] ?? '';
unset($_SESSION['mensagem'], $_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Lanchonete - Produtos</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<style>

/* nossos-produtos */
.nossos-produtos {
    max-width: 1200px;
    margin: 50px auto;
    padding: 0 20px;
    text-align: center;
}

.nossos-produtos h2 {
    font-size: 2.5em;
    color: #333;
    margin-bottom: 40px;
    position: relative;
    display: inline-block;
}

.nossos-produtos h2::after {
    content: '';
    position: absolute;
    left: 50%;
    bottom: -10px;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, #e44d26 0%, #c23b1f 100%);
    border-radius: 2px;
}

/* cards de produtos */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 30px;
    margin: 50px auto 0;
    max-width: 1200px;
    padding: 0 20px;
}

.card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
}

.card-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.card:hover .card-img {
    transform: scale(1.05);
}

.card-content {
    padding: 25px;
}

.card-title {
    font-size: 1.5rem;
    margin-bottom: 10px;
    color: #2c3e50;
    font-weight: 700;
}

.card-text {
    color: #666;
    line-height: 1.6;
    margin-bottom: 10px;
    font-size: 0.95rem;
}

.card-categoria {
    color: #777;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.categoria-badge {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.card-preco {
    font-size: 1.6rem;
    margin-bottom: 20px;
    color: #e44d26;
    font-weight: 700;
}

/* Formulário */
.form-add-carrinho {
    display: flex;
    flex-direction: column;
    gap: 15px;
    align-items: center;
}

.quantity-selector {
    display: inline-flex;
    align-items: center;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.quantity-btn {
    width: 40px;
    height: 45px;
    border: none;
    background: #f8f9fa;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    user-select: none;
}

.quantity-btn:hover:not(:disabled) {
    background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
    color: white;
    transform: scale(1.05);
}

.quantity-btn:active {
    transform: scale(0.95);
}

.quantity-btn:disabled {
    background: #f8f9fa;
    color: #ccc;
    cursor: not-allowed;
    opacity: 0.5;
}

.quantity-value {
    min-width: 50px;
    text-align: center;
    font-size: 18px;
    font-weight: 600;
    padding: 0 15px;
    color: #333;
    background: white;
}

.card-btn {
    width: 100%;
    background: linear-gradient(135deg, #e44d26 0%, #c23b1f 100%);
    border: none;
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.card-btn:hover {
    background: linear-gradient(135deg, #c23b1f 0%, #a33217 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(228, 77, 38, 0.3);
}

.card-btn:active {
    transform: translateY(0);
}

.card-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Feedback visual para adição bem-sucedida */
.success-feedback {
    background: linear-gradient(135deg, #27ae60 0%, #229954 100%) !important;
}

.success-feedback .btn-text::after {
    content: " ✓";
}

/* Responsividade */
@media (max-width: 768px) {
    .cards {
        grid-template-columns: 1fr;
        margin-left: 0;
        gap: 20px;
    }
    
    .nossos-produtos h2 {
        font-size: 2rem;
    }
    
    .card-content {
        padding: 20px;
    }
    
    .quantity-selector {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .cards {
        grid-template-columns: 1fr;
        padding: 0 10px;
    }
    
    .card-title {
        font-size: 1.3rem;
    }
    
    .card-preco {
        font-size: 1.4rem;
    }
}


</style>


<div id="produtos" class="nossos-produtos">
    <h2>Nossos Produtos</h2> 
</div>

<?php if ($mensagem): ?><div class="mensagem"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?>
<?php if ($erro): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

<div class="produtos cards">
    <?php foreach ($produtos as $p): ?>
        <div class="card">
            <img class="card-img" src="<?= htmlspecialchars($p['imagem']) ?>" alt="<?= htmlspecialchars($p['nome']) ?>">
            <div class="card-content">
                <h3 class="card-title"><?= htmlspecialchars($p['nome']) ?></h3>
                <p class="card-text"><?= htmlspecialchars($p['detalhes']) ?></p>
                <p class="card-preco"><?= htmlspecialchars($p['preco']) ?></p>
                <form action="carrinho.php" method="post" class="form-add-carrinho">
                    <?php foreach ($p as $campo => $valor): ?>
                        <input type="hidden" name="<?= htmlspecialchars($campo) ?>" value="<?= htmlspecialchars($valor) ?>">
                    <?php endforeach; ?>
                    <div class="quantity-selector">
                        <button type="button" class="quantity-btn" onclick="this.nextElementSibling.stepDown()">-</button>
                        <input type="number" name="quantidade" value="1" min="1" class="quantity-value">
                        <button type="button" class="quantity-btn" onclick="this.previousElementSibling.stepUp()">+</button>
                    </div>
                    <button type="submit" name="adicionar" class="card-btn">Adicionar ao Carrinho</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<br>

</body>
</html>
