<?php
session_start();
require_once 'db.php';
include 'verificar_login.php'; // ✅ Proteção unificada

$vendedorEmail = $_SESSION['usuario']['email'];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verificar se é uma atualização (ID presente) ou criação (sem ID)
    $id = intval($_POST['id'] ?? 0);
    
    if ($id > 0) {
        // É uma atualização, redirecionar para updateProduto.php
        header("Location: updateProduto.php");
        exit;
    }
    
    $nome = trim($_POST['nome'] ?? '');
    $categoria = trim($_POST['categoria'] ?? '');
    $preco = floatval($_POST['preco'] ?? 0);
    $descricao = trim($_POST['descricao'] ?? '');
    $status = trim($_POST['status'] ?? 'Ativo');
    $arquivo_produto = null;    // Processar upload do arquivo se fornecido
    if (isset($_FILES['arquivo_produto']) && $_FILES['arquivo_produto']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/produtos/';
          // Verificar se o diretório existe, criar se não existir
        if (!is_dir($upload_dir)) {
            if (!mkdir($upload_dir, 0755, true)) {
                header("Location: ../pages/produto.php?erro=erro_diretorio");
                exit();
            }
        }
        
        // Verificar se o diretório tem permissão de escrita
        if (!is_writable($upload_dir)) {
            header("Location: ../pages/produto.php?erro=erro_permissao");
            exit();
        }
        
        $arquivo_nome = $_FILES['arquivo_produto']['name'];
        $arquivo_tmp = $_FILES['arquivo_produto']['tmp_name'];
        $arquivo_tamanho = $_FILES['arquivo_produto']['size'];
        $arquivo_ext = strtolower(pathinfo($arquivo_nome, PATHINFO_EXTENSION));
        
        // Extensões permitidas
        $extensoes_permitidas = ['pdf', 'doc', 'docx', 'zip', 'rar', 'txt', 'mp4', 'mp3', 'png', 'jpg', 'jpeg'];
          // Verificar tamanho (10MB máximo)
        if ($arquivo_tamanho > 10 * 1024 * 1024) {
            header("Location: ../pages/produto.php?erro=arquivo_grande");
            exit();
        }
        
        // Verificar extensão
        if (!in_array($arquivo_ext, $extensoes_permitidas)) {
            header("Location: ../pages/produto.php?erro=tipo_arquivo");
            exit();
        }
        
        // Gerar nome único para o arquivo
        $nome_unico = uniqid() . '_' . time() . '.' . $arquivo_ext;
        $caminho_completo = $upload_dir . $nome_unico;
          // Mover arquivo para diretório de uploads
        if (move_uploaded_file($arquivo_tmp, $caminho_completo)) {
            $arquivo_produto = 'uploads/produtos/' . $nome_unico;
        } else {
            header("Location: ../pages/produto.php?erro=falha_upload");
            exit();
        }
    }   
    if ($nome && $categoria && $preco && $descricao && $vendedorEmail) {
        $sql = "INSERT INTO produtos (nome, categoria, preco, descricao, status, vendedor_email, arquivo_produto)
                VALUES (?, ?, ?, ?, ?, ?, ?)";        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            header("Location: ../pages/produto.php?erro=erro_prepare");
            exit();
        }

        $stmt->bind_param("ssdssss", $nome, $categoria, $preco, $descricao, $status, $vendedorEmail, $arquivo_produto);

        if ($stmt->execute()) {
            header("Location: ../pages/produto.php?sucesso=1");
            exit;
        } else {
            header("Location: ../pages/produto.php?erro=erro_inserir");
            exit();
        }
    } else {
        header("Location: ../pages/produto.php?erro=campos_obrigatorios");
        exit();
    }
} else {
    header("Location: ../pages/produto.php?erro=requisicao_invalida");
    exit();
}
