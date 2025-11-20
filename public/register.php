<?php
session_start();
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? ''); // Remove não-números
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirma_senha'] ?? '';

    // Validações
    if (empty($nome)) $errors[] = 'Nome é obrigatório.';
    if (empty($cpf)) $errors[] = 'CPF é obrigatório.';
    if (strlen($cpf) != 11) $errors[] = 'CPF deve ter 11 dígitos.';
    if (empty($senha)) $errors[] = 'Senha é obrigatória.';
    if ($senha !== $confirma_senha) $errors[] = 'As senhas não conferem.';

    // Verifica se CPF já existe
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE cpf = ?');
        $stmt->execute([$cpf]);
        if ($stmt->fetch()) {
            $errors[] = 'Este CPF já está cadastrado.';
        }
    }

    // Se não houver erros, cria o usuário
    if (empty($errors)) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (nome, cpf, password_hash) VALUES (?, ?, ?)');
        
        try {
            $stmt->execute([$nome, $cpf, $hash]);
            // Redireciona para o login com uma msg de sucesso
            header('Location: login.php?registration=success');
            exit;
        } catch (PDOException $e) {
            $errors[] = 'Erro ao cadastrar. Tente novamente.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-br" class="h-full bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Sprint Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
     <style>
        body {
            background-image: linear-gradient(to right top, #0c2a5d, #004e8d, #0076ad, #00a0a7, #00c780);
        }
    </style>
</head>
<body class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8 rounded-lg bg-white p-10 shadow-2xl">
        <div>
            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                Crie sua conta
            </h2>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded" role="alert">
                <p class="font-bold">Opa! Algo deu errado:</p>
                <ul class="list-disc pl-5 mt-2 text-sm">
                    <?php foreach ($errors as $error): ?>
                        <li><?= h($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="mt-8 space-y-6" action="register.php" method="POST">
            <div class="rounded-md shadow-sm space-y-4">
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome Completo</label>
                    <input id="nome" name="nome" type="text" required
                           class="mt-1 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"
                           value="<?= h($_POST['nome'] ?? '') ?>">
                </div>
                <div>
                    <label for="cpf" class="block text-sm font-medium text-gray-700">CPF (apenas números)</label>
                    <!-- 
                        *** AQUI A MUDANÇA: adicionei maxlength="11" *** -->
                    <input id="cpf" name="cpf" type="text" required maxlength="11"
                           class="mt-1 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"
                           value="<?= h($_POST['cpf'] ?? '') ?>">
                </div>
                <div>
                    <label for="senha" class="block text-sm font-medium text-gray-700">Senha</label>
                    <input id="senha" name="senha" type="password" required
                           class="mt-1 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
                <div>
                    <label for="confirma_senha" class="block text-sm font-medium text-gray-700">Confirmar Senha</label>
                    <input id="confirma_senha" name="confirma_senha" type="password" required
                           class="mt-1 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
            </div>

            <div>
                <button type="submit"
                        class="group relative flex w-full justify-center rounded-md bg-indigo-600 py-2.5 px-3 text-sm font-semibold text-white hover:bg-indigo-500 focus-visible:outline-indigo-600">
                    Criar Conta
                </button>
            </div>
        </form>
         <p class="mt-10 text-center text-sm text-gray-500">
            Já tem uma conta?
            <a href="login.php" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                Faça login
            </a>
        </p>
    </div>
</body>
</html>