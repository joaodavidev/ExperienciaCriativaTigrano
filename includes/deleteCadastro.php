<?php
session_start();
require_once '../includes/db.php';
include 'verificar_login.php';

$email = $_SESSION['usuario']['email'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deletar_conta'])) {
    
    // Verificar se o usuário já vendeu algum produto
    $stmtVerifica = $conn->prepare("SELECT COUNT(*) as total_vendas FROM vendas v 
                                   INNER JOIN produtos p ON v.produto_id = p.id 
                                   WHERE p.vendedor_email = ?");
    $stmtVerifica->bind_param("s", $email);
    $stmtVerifica->execute();
    $resultVerifica = $stmtVerifica->get_result();
    $vendas = $resultVerifica->fetch_assoc();
    $stmtVerifica->close();

    if ($vendas['total_vendas'] > 0) {
        // Se já vendeu produtos, não pode deletar a conta, apenas desativar
        // Primeiro, vamos adicionar um campo 'ativo' na tabela usuarios se não existir
        
        // Verificar se a coluna 'ativo' existe
        $resultColumn = $conn->query("SHOW COLUMNS FROM usuarios LIKE 'ativo'");
        if ($resultColumn->num_rows == 0) {
            // Adicionar coluna 'ativo' se não existir
            $conn->query("ALTER TABLE usuarios ADD COLUMN ativo TINYINT(1) DEFAULT 1");
        }
        
        // Desativar a conta ao invés de deletar
        $sqlDesativar = "UPDATE usuarios SET ativo = 0 WHERE email = ?";
        $stmtDesativar = $conn->prepare($sqlDesativar);
        $stmtDesativar->bind_param("s", $email);

        if ($stmtDesativar->execute()) {
            $stmtDesativar->close();
            
            // Destruir a sessão já que a conta foi desativada
            session_destroy();
            
            header("Location: ../pages/login.php?conta_desativada=1");
            exit();
        } else {
            $stmtDesativar->close();
            header("Location: ../pages/configuracoes.php?erro=Erro ao desativar a conta");
            exit();
        }
        
    } else {
        // Se nunca vendeu nada, pode deletar normalmente
        $sql = "DELETE FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);

        if ($stmt->execute()) {
            $stmt->close();
            
            // Destruir a sessão
            session_destroy();
            
            header("Location: ../pages/login.php?sucesso=1");
            exit();
        } else {
            $stmt->close();
            header("Location: ../pages/configuracoes.php?erro=Erro ao deletar a conta");
            exit();
        }
    }
}
?>
