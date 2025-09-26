<?php
session_start();

// Função para converter preço string → número
function converterPreco($precoStr) {
    if (empty($precoStr)) return 0.0;
    $numero = preg_replace('/[^\d,]/', '', $precoStr);
    $numero = str_replace(',', '.', $numero);
    return floatval($numero);
}

// Função para validar e sanitizar dados do produto
function validarProduto($dados) {
    return [
        'nome' => trim($dados['nome'] ?? ''),
        'preco' => trim($dados['preco'] ?? 'R$ 0,00'),
        'detalhes' => trim($dados['detalhes'] ?? ''),
        'categoria' => trim($dados['categoria'] ?? ''),
        'imagem' => trim($dados['imagem'] ?? ''),
        'quantidade' => max(1, intval($dados['quantidade'] ?? 1))
    ];
}

// Inicializa carrinho e compras se não existirem
if (!isset($_SESSION['carrinho'])) $_SESSION['carrinho'] = [];
if (!isset($_SESSION['compras'])) $_SESSION['compras'] = [];

// Processar ações POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['adicionar'])) {
        $produto = validarProduto($_POST);
        if (!empty($produto['nome'])) {
            if (isset($_SESSION['carrinho'][$produto['nome']])) {
                $_SESSION['carrinho'][$produto['nome']]['quantidade'] += $produto['quantidade'];
            } else {
                $_SESSION['carrinho'][$produto['nome']] = $produto;
            }
            $_SESSION['mensagem'] = "Produto '{$produto['nome']}' adicionado ao carrinho!";
        } else {
            $_SESSION['erro'] = "Nome do produto é obrigatório!";
        }
        header("Location: carrinho.php"); exit;
    }
    elseif (isset($_POST['atualizar'])) {
        if (isset($_POST['quantidades']) && is_array($_POST['quantidades'])) {
            foreach ($_POST['quantidades'] as $nome => $qtd) {
                $qtd = intval($qtd);
                $nome = trim($nome);
                if (!empty($nome) && isset($_SESSION['carrinho'][$nome])) {
                    if ($qtd > 0) $_SESSION['carrinho'][$nome]['quantidade'] = $qtd;
                    else unset($_SESSION['carrinho'][$nome]);
                }
            }
            $_SESSION['mensagem'] = "Carrinho atualizado com sucesso!";
        }
        header("Location: carrinho.php"); exit;
    }
    elseif (isset($_POST['finalizar'])) {
        if (!empty($_SESSION['carrinho'])) {
            $total = 0;
            foreach ($_SESSION['carrinho'] as $produto) {
                $precoNum = converterPreco($produto['preco'] ?? 'R$ 0,00');
                $quantidade = $produto['quantidade'] ?? 0;
                $total += $precoNum * $quantidade;
            }
            $_SESSION['compras'][] = [
                'itens' => $_SESSION['carrinho'],
                'data' => date("d/m/Y H:i"),
                'total' => $total
            ];
            $_SESSION['carrinho'] = [];
            $_SESSION['mensagem'] = "Compra finalizada com sucesso!";
            header("Location: pagamento.php"); exit;
        } else {
            $_SESSION['erro'] = "Carrinho está vazio!";
            header("Location: carrinho.php"); exit;
        }
    }
}

// Processar ações GET
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['remover'])) {
    $produtoRemover = trim($_GET['remover']);
    if (!empty($produtoRemover) && isset($_SESSION['carrinho'][$produtoRemover])) {
        unset($_SESSION['carrinho'][$produtoRemover]);
        $_SESSION['mensagem'] = "Produto removido do carrinho!";
    } else {
        $_SESSION['erro'] = "Produto não encontrado no carrinho!";
    }
    header("Location: carrinho.php"); exit;
}

$mensagem = $_SESSION['mensagem'] ?? '';
$erro = $_SESSION['erro'] ?? '';
unset($_SESSION['mensagem'], $_SESSION['erro']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Meu Carrinho</title>
    <style>
        body { font-family: Arial; background: #f5f6fa; padding: 20px; }
        h1 { text-align: center; }
        .mensagem, .erro {
            padding: 10px; text-align: center; margin: 20px auto; width: 80%;
            border-radius: 8px; font-weight: bold;
        }
        .mensagem { background: #d4edda; color: #155724; }
        .erro { background: #f8d7da; color: #721c24; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #c0392b; color: #fff; }
        .btn-remover {
            background: #e74c3c; color: #fff; padding: 5px 10px;
            border: none; border-radius: 5px; cursor: pointer;
        }
        .btn { padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-atualizar { background: #2980b9; color: #fff; }
        .btn-finalizar { background: #27ae60; color: #fff; }
        .btn-voltar {
            background: #7f8c8d;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            text-decoration: none;
            margin-left: 10px;
        }
        .btn-voltar:hover {
            background: #95a5a6;
        }

    </style>
</head>
<body>
    <h1>🛒 Meu Carrinho</h1>

    <?php if ($mensagem): ?><div class="mensagem"><?= htmlspecialchars($mensagem) ?></div><?php endif; ?>
    <?php if ($erro): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>

    <?php if (empty($_SESSION['carrinho'])): ?>
        <p style="text-align:center;">Carrinho vazio. <a href="index.php">Ver produtos</a></p>
    <?php else: ?>
        <form method="post">
            <table>
                <tr>
                    <th>Imagem</th><th>Produto</th><th>Detalhes</th><th>Categoria</th>
                    <th>Preço</th><th>Qtd</th><th>Subtotal</th><th>Ação</th>
                </tr>
                <?php $total=0; foreach ($_SESSION['carrinho'] as $produto): 
                    $nome = htmlspecialchars($produto['nome']);
                    $detalhes = htmlspecialchars($produto['detalhes']);
                    $cat = htmlspecialchars($produto['categoria']);
                    $img = htmlspecialchars($produto['imagem']);
                    $preco = $produto['preco'];
                    $qtd = intval($produto['quantidade']);
                    $precoNum = converterPreco($preco);
                    $subtotal = $qtd * $precoNum;
                    $total += $subtotal;
                ?>
                <tr>
                    <td><img src="<?= $img ?>" width="60"></td>
                    <td><?= $nome ?></td>
                    <td><?= $detalhes ?></td>
                    <td><?= $cat ?></td>
                    <td><?= $preco ?></td>
                    <td><input type="number" name="quantidades[<?= $nome ?>]" value="<?= $qtd ?>" min="0"></td>
                    <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
                    <td>
                        <a href="carrinho.php?remover=<?= urlencode($nome) ?>" 
                           onclick="return confirm('Remover este produto?')">
                            <div class="btn-remover">🗑️</div>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
           <p style="text-align:right; font-size:1.2em; font-weight:bold;">
                Total: R$ <?= number_format($total, 2, ',', '.') ?>
            </p>
            <div style="text-align:center; margin-top:20px;">
                <button type="submit" name="atualizar" class="btn btn-atualizar">🔄 Atualizar</button>
                <button type="submit" name="finalizar" class="btn btn-finalizar">💳 Finalizar Compra</button>
                <a href="index.php" class="btn btn-voltar">⬅️ Voltar às Compras</a>
            </div>

        </form>
    <?php endif; ?>

<script>
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
<?php if (!empty($_SESSION['carrinho'])): ?>
window.addEventListener('beforeunload', function (e) {
    e.preventDefault();
    e.returnValue = 'Você tem itens no carrinho. Deseja sair mesmo assim?';
});
<?php endif; ?>
</script>
</body>
</html>
