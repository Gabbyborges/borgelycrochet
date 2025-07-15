<?php
include('../db.php');  

// Verifica se o ID foi passado
if (!isset($_GET['id'])) {
    $error = "ID não informado.";
} else {
    $id = $_GET['id'];
    
    // Deleta o registro
    $sql = "DELETE FROM financeiro WHERE id=$id";
    
    if ($conn->query($sql) === TRUE) {
        $success = true;
    } else {
        $error = "Erro ao excluir: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Excluir Registro</title>
    <style>
        :root {
            --primary-color: #b86f87;
            --soft-pink: #fce4ec;
            --white: #ffffff;
            --gray-50: #f9fafb;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--soft-pink) 0%, var(--gray-50) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gray-800);
        }

        .container {
            background: var(--white);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            text-align: center;
            max-width: 400px;
            width: 90%;
            border-top: 4px solid var(--primary-color);
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .success-icon {
            color: #16a34a;
        }

        .error-icon {
            color: #dc2626;
        }

        h1 {
            color: var(--primary-color);
            font-size: 1.5rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .message {
            color: var(--gray-600);
            margin-bottom: 2rem;
            font-size: 1rem;
        }

        .btn {
            background: var(--primary-color);
            color: var(--white);
            padding: 0.75rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn:hover {
            background: var(--primary-color);
            transform: translateY(-2px);
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if (isset($success) && $success) { ?>
            <div class="icon success-icon">✅</div>
            <h1>Sucesso!</h1>
            <p class="message">Registro excluído com sucesso!</p>
        <?php } else { ?>
            <div class="icon error-icon">❌</div>
            <h1>Erro</h1>
            <p class="message"><?= htmlspecialchars($error) ?></p>
        <?php } ?>
        
        <a href="financeiro.php" class="btn">
            <span>↩️</span> Voltar ao Painel
        </a>
    </div>
</body>
</html>