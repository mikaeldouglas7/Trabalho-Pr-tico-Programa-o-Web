<?php
// sprint_manager/public/projects.php
require_once __DIR__ . '/../src/db.php';
$page_title = 'Projetos';
require_once __DIR__ . '/_header.php';

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create') {
    $nome = trim($_POST['nome'] ?? '');
    $origem = trim($_POST['origem'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    
    if (empty($nome)) {
        $error = 'O nome do projeto é obrigatório.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO projects (nome, origem, descricao, created_by) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nome, $origem, $descricao, $user_id]);
        header('Location: projects.php');
        exit;
    }
}

// Busca todos os projetos
$projects = $pdo->query('SELECT * FROM projects ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Coluna do Formulário -->
    <div class="md:col-span-1">
        <form action="projects.php" method="POST" class="bg-white shadow-sm rounded-lg overflow-hidden">
            <input type="hidden" name="action" value="create">
            
            <div class="px-6 py-5">
                <h2 class="text-xl font-semibold text-gray-900">Criar Novo Projeto</h2>
            </div>
            
            <div class="border-t border-gray-200 p-6 space-y-4">
                <?php if ($error): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded" role="alert">
                        <?= h($error) ?>
                    </div>
                <?php endif; ?>
                
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700">Nome do Projeto</label>
                    <input type="text" name="nome" id="nome" required
                           class="mt-1 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
                
                <div>
                    <label for="origem" class="block text-sm font-medium text-gray-700">Origem (Ex: Cliente, Interno)</label>
                    <input type="text" name="origem" id="origem"
                           class="mt-1 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>

                <div>
                    <label for="descricao" class="block text-sm font-medium text-gray-700">Descrição</label>
                    <textarea name="descricao" id="descricao" rows="4"
                              class="mt-1 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                </div>
            </div>

            <div class="bg-gray-50 px-6 py-4 text-right">
                <button type="submit"
                        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Salvar Projeto
                </button>
            </div>
        </form>
    </div>

    <!-- Coluna da Lista -->
    <div class="md:col-span-2">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-5">
                <h2 class="text-xl font-semibold text-gray-900">Seus Projetos</h2>
            </div>
            <div class="border-t border-gray-200">
                <?php if (empty($projects)): ?>
                    <p class="text-sm text-gray-600 p-6">Nenhum projeto criado ainda.</p>
                <?php else: ?>
                    <ul role="list" class="divide-y divide-gray-100">
                        <?php foreach ($projects as $p): ?>
                            <li class="flex items-center justify-between gap-x-6 px-6 py-5">
                                <div>
                                    <p class="text-base font-semibold leading-6 text-gray-900"><?= h($p['nome']) ?></p>
                                    <p class="mt-1 text-xs leading-5 text-gray-500">
                                        Origem: <?= h($p['origem'] ?? 'N/A') ?> | 
                                        Criado em: <?= h(date('d/m/Y', strtotime($p['created_at']))) ?>
                                    </p>
                                </div>
                                <a href="project_view.php?id=<?= $p['id'] ?>"
                                   class="rounded-md bg-white px-3 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                    Ver Detalhes
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/_footer.php';
?>