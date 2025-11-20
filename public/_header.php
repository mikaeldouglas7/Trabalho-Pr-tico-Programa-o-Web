
<?php
// sprint_manager/public/_header.php
require_once __DIR__ . '/../src/helpers.php';
// Garante que o usuário está logado para qualquer página que incluir este header
ensure_logged_in(); 

$user_name = $_SESSION['user_name'] ?? 'Usuário';
$current_page = basename($_SERVER['PHP_SELF']);

// Define os links de navegação
$menu_items = [
    'dashboard.php' => 'Dashboard',
    'projects.php' => 'Projetos',
    'ponto.php' => 'Meu Ponto'
];
?>
<!doctype html>
<html lang="pt-br" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Define o título da página (será definido por cada página) -->
    <title><?= h($page_title ?? 'Sprint Manager') ?></title>
    
    <!-- Carrega o Tailwind CSS (permitido pelo PDF) -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Carrega o script do timer (precisamos dele em todas as páginas) -->
    <script src="../assets/js/timer.js" defer></script>
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Menu de Navegação Principal -->
        <nav class="bg-gray-800">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <!-- Ícone -->
                            <svg class="h-8 w-8 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18c-2.305 0-4.408.867-6 2.292m0-14.25v14.25" />
                            </svg>
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <?php foreach ($menu_items as $file => $name): ?>
                                    <?php $is_active = ($current_page === $file); ?>
                                    <a href="<?= $file ?>" 
                                       class="<?= $is_active ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' ?> rounded-md px-3 py-2 text-sm font-medium">
                                        <?= $name ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            <span class="text-gray-400 text-sm mr-3">Olá, <?= h($user_name) ?></span>
                            <!-- Botão de Sair -->
                            <a href="logout.php" class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">Sair</a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Cabeçalho da Página (Título) -->
        <header class="bg-white shadow">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                    <?= h($page_title ?? 'Dashboard') // A página específica define $page_title ?>
                </h1>
            </div>
        </header>
        
        <!-- Conteúdo Principal -->
        <main>
            <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
                <!-- O conteúdo da sua página (dashboard.php, ponto.php, etc.) começa aqui -->