<?php
session_start();
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php'; // para a função h()

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($cpf) || empty($senha)) {
        $error = 'CPF e senha são obrigatórios.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nome, password_hash, is_admin FROM users WHERE cpf = ? LIMIT 1');
        $stmt->execute([$cpf]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($senha, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nome'];
            $_SESSION['is_admin'] = $user['is_admin'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'CPF ou senha inválidos.';
        }
    }
}
?>
<!doctype html>
<html lang="pt-br" class="h-full bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sprint Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Fundo gradiente "divertido" */
        body {
            background-image: linear-gradient(to right top, #0c2a5d, #004e8d, #0076ad, #00a0a7, #00c780);
        }
    </style>
</head>
<body class="flex min-h-full items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8 rounded-lg bg-white p-10 shadow-2xl">
        <div>
            <!-- Ícone -->
            <svg class="mx-auto h-12 w-auto text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
            </svg>

            <h2 class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900">
                Sprint Manager
            </h2>
             <p class="mt-2 text-center text-sm text-gray-600">
                Acesse sua conta para começar
             </p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded" role="alert">
                <p><?= h($error) ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['registration']) && $_GET['registration'] === 'success'): ?>
             <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded" role="alert">
                <p>Cadastro realizado com sucesso! Faça seu login.</p>
            </div>
        <?php endif; ?>


        <form class="mt-8 space-y-6" action="login.php" method="POST">
            <!-- 
                *** CORREÇÃO AQUI ***
                O erro estava aqui. Eu tinha um <div> a mais envolvendo o input do CPF
                que não deveria existir. Agora está correto, com os dois inputs
                diretamente dentro do div principal.
            -->
            <div class="-space-y-px rounded-md shadow-sm">
                
                <div>
                    <label for="cpf" class="sr-only">CPF</label>
                    <input id="cpf" name="cpf" type="text" required maxlength="11"
                           class="relative block w-full rounded-t-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                           placeholder="Seu CPF (só números)" value="<?= h($_POST['cpf'] ?? '') ?>">
                </div>
                
                <div>
                    <label for="senha" class="sr-only">Senha</label>
                    <input id="senha" name="senha" type="password" required
                           class="relative block w-full rounded-b-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                           placeholder="Sua senha">
                </div>
                
            </div>
            <!-- Fim do bloco corrigido -->


            <div>
                <button type="submit"
                        class="group relative flex w-full justify-center rounded-md bg-indigo-600 py-2.5 px-3 text-sm font-semibold text-white hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Entrar
                </button>
            </div>
        </form>

        <p class="mt-10 text-center text-sm text-gray-500">
            Não tem uma conta?
            <a href="register.php" class="font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                Cadastre-se aqui
            </a>
        </p>
    </div>
</body>
</html>