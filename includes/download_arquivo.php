<?php
session_start();
require_once '../includes/db.php';
include '../includes/verificar_login.php';

// Verificar se o arquivo foi solicitado
if (!isset($_GET['arquivo']) || empty($_GET['arquivo'])) {
    http_response_code(404);
    die('Arquivo não encontrado.');
}

$arquivo_solicitado = $_GET['arquivo'];
$email_usuario = $_SESSION['usuario']['email'];

try {
    // Verificar se o usuário tem permissão para baixar este arquivo
    $stmt = $conn->prepare("SELECT 
            p.arquivo_produto,
            p.nome as produto_nome
        FROM vendas v
        INNER JOIN produtos p ON v.produto_id = p.id
        WHERE v.comprador_email = ? AND p.arquivo_produto = ?
        LIMIT 1");

    if (!$stmt) {
        throw new Exception("Erro ao preparar statement: " . $conn->error);
    }

    $stmt->bind_param("ss", $email_usuario, $arquivo_solicitado);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(403);
        die('Você não tem permissão para baixar este arquivo.');
    }

    $produto = $result->fetch_assoc();
    $stmt->close();

    // Caminho completo do arquivo
    $caminho_arquivo = '../' . $arquivo_solicitado;

    // Verificar se o arquivo existe fisicamente
    if (!file_exists($caminho_arquivo)) {
        http_response_code(404);
        die('Arquivo não encontrado no servidor.');
    }

    // Obter informações do arquivo
    $nome_arquivo = basename($caminho_arquivo);
    $extensao = pathinfo($nome_arquivo, PATHINFO_EXTENSION);
    $tamanho_arquivo = filesize($caminho_arquivo);

    // Definir cabeçalhos para download
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $produto['produto_nome'] . '.' . $extensao . '"');
    header('Content-Length: ' . $tamanho_arquivo);
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Enviar arquivo
    readfile($caminho_arquivo);
    exit;

} catch (Exception $e) {
    error_log("Erro no download: " . $e->getMessage());
    http_response_code(500);
    die('Erro interno do servidor.');
}
?>
