<?php
session_start();

// Se não houver compras finalizadas, redireciona
if (empty($_SESSION['compras'])) {
    header("Location: carrinho.php");
    exit;
}

// Pega a última compra realizada
$compra = end($_SESSION['compras']);
$itens = $compra['itens'];
$total = $compra['total'];
$data = $compra['data'];

// Processar pagamento (suporta AJAX e fallback normal)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirmar'])) {
    // Simulação de processamento: aqui você poderia validar os dados recebidos
    // Resposta AJAX (se 'ajax' enviado) -> retornar JSON
    $isAjax = !empty($_POST['ajax']) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

    // (Opcional) validar campos mínimos
    $nomeCartao = trim($_POST['nome'] ?? '');
    if ($nomeCartao === '') {
        if ($isAjax) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'message' => 'Nome no cartão é obrigatório.']);
            exit;
        } else {
            $_SESSION['erro'] = "Nome no cartão é obrigatório.";
            header("Location: pagamento.php");
            exit;
        }
    }

    // Aqui você faria integração com gateway; vamos simular sucesso:
    $_SESSION['mensagem'] = "Pagamento confirmado! Obrigado pela sua compra.";

    // Caso queira, pode limpar compras/carrinho:
    // $_SESSION['carrinho'] = [];
    // $_SESSION['compras'] = []; // ou manter histórico

    if ($isAjax) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => true, 'message' => $_SESSION['mensagem']]);
        exit;
    } else {
        // Fallback sem JS: redireciona para index com mensagem
        header("Location: index.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Pagamento</title>
    <style>
        body { font-family: Arial; background: #f5f6fa; padding: 20px; }
        h1, h2 { text-align: center; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
        th { background: #c0392b; color: #fff; }
        .resumo { margin: 20px auto; max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; }
        .form-pagamento { margin: 20px auto; max-width: 600px; background: #fff; padding: 20px; border-radius: 8px; }
        label { display: block; margin: 10px 0 5px; font-weight: bold; }
        input, select { width: 100%; padding: 8px; margin-bottom: 15px; border-radius: 5px; border: 1px solid #ccc; }
        .btn { padding: 12px 20px; background: #27ae60; color: #fff; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #219150; }
    </style>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <h1>💳 Tela de Pagamento</h1>

    <div class="resumo">
        <h2>Resumo da Compra</h2>
        <p><b>Data:</b> <?= htmlspecialchars($data) ?></p>
        <table>
            <tr>
                <th>Produto</th><th>Qtd</th><th>Preço</th><th>Subtotal</th>
            </tr>
            <?php foreach ($itens as $produto): 
                $nome = htmlspecialchars($produto['nome']);
                $qtd = intval($produto['quantidade']);
                $preco = $produto['preco'];
                $precoNum = floatval(str_replace(',', '.', preg_replace('/[^\d,]/', '', $preco)));
                $subtotal = $qtd * $precoNum;
            ?>
            <tr>
                <td><?= $nome ?></td>
                <td><?= $qtd ?></td>
                <td><?= $preco ?></td>
                <td>R$ <?= number_format($subtotal, 2, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <p style="text-align:right; font-size:1.2em; font-weight:bold;">
            Total: R$ <?= number_format($total, 2, ',', '.') ?>
        </p>
    </div>

    <div class="form-pagamento">
        <h2>Dados do Pagamento</h2>
        <!-- OBS: Mantemos método POST para fallback sem JS -->
        <form method="post" id="formPagamento">
            <label for="nome">Seu Nome</label>
            <input type="text" id="nome" name="nome" required>

            <label for="numero">Número de Telefone</label>
            <input type="text" id="numero" name="numero" maxlength="16" required>

            <label for="validade">Endereço</label>
            <input type="text" id="nome" name="nome" required>

            <label for="metodo">Método de pagamento:</label>
            <select id="metodo" name="metodo" required>
                <option value="credito">Cartão de Crédito</option>
                <option value="debito">Cartão de Débito</option>
                <option value="pix">PIX</option>
                <option value="boleto">Boleto</option>
            </select>

            <button type="submit" name="confirmar" class="btn" id="btnConfirmar">Confirmar Pagamento</button>
        </form>
    </div>

<script>
document.getElementById("formPagamento").addEventListener("submit", function(e) {
    e.preventDefault(); // impede envio normal para usarmos AJAX + SweetAlert
    const form = this;

    Swal.fire({
        title: 'Confirmar pagamento?',
        text: "Você está prestes a finalizar sua compra.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#27ae60',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, confirmar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Mostra loader enquanto processa
            Swal.fire({
                title: 'Processando pagamento...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Prepara dados do formulário
            const fd = new FormData(form);
            fd.append('confirmar', '1'); // garante que o POST contenha o field
            fd.append('ajax', '1'); // sinaliza para o PHP responder em JSON

            fetch('pagamento.php', {
                method: 'POST',
                body: fd,
                credentials: 'same-origin'
            })
            .then(response => {
                // tenta parsear JSON; se der erro, lança para catch
                return response.json();
            })
            .then(data => {
                Swal.close();
                if (data.success) {
                    Swal.fire({
                        title: 'Pagamento confirmado!',
                        text: data.message || 'Obrigada pela compra.',
                        icon: 'success',
                        confirmButtonText: 'Ir para início'
                    }).then(() => {
                        // Redireciona para index.php
                        window.location.href = 'index.php';
                    });
                } else {
                    Swal.fire('Erro', data.message || 'Erro ao processar pagamento.', 'error');
                }
            })
            .catch((err) => {
                Swal.close();
                Swal.fire('Erro', 'Não foi possível processar o pagamento. Tente novamente.', 'error');
                console.error(err);
            });
        }
        // se cancelar, não faz nada
    });
});
</script>

</body>
</html>
