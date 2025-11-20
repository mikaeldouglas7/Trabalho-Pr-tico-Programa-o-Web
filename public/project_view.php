<?php
// sprint_manager/public/project_view.php
require_once __DIR__ . '/../src/db.php';
$page_title = 'Detalhes do Projeto';
require_once __DIR__ . '/_header.php';

$project_id = intval($_GET['id'] ?? 0);
if (!$project_id) {
    echo '<p class="text-red-600">Projeto não encontrado.</p>';
    require_once __DIR__ . '/_footer.php';
    exit;
}

// Lógica para CRIAR SPRINT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_sprint') {
    $duracao_days = intval($_POST['duracao_days'] ?? 0);
    $start_date_str = $_POST['start_date'] ?? '';
    
    if ($duracao_days > 0 && !empty($start_date_str)) {
        try {
            $start_date = new DateTime($start_date_str);
            $end_date = (clone $start_date)->modify("+$duracao_days days");

            $stmt = $pdo->prepare(
                'INSERT INTO sprints (project_id, duracao_days, start_date, end_date) 
                 VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([
                $project_id, 
                $duracao_days, 
                $start_date->format('Y-m-d'), 
                $end_date->format('Y-m-d')
            ]);
            // Recarrega para limpar o POST
            header("Location: project_view.php?id=$project_id");
            exit;
        } catch (Exception $e) {
            $error = 'Data de início inválida.';
        }
    } else {
        $error = 'Duração e data de início são obrigatórias.';
    }
}

// Busca dados do projeto
$stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
$stmt->execute([$project_id]);
$p = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$p) {
    echo '<p class="text-red-600">Projeto não encontrado.</p>';
    require_once __DIR__ . '/_footer.php';
    exit;
}

// Busca sprints do projeto
$sprints = $pdo->prepare('SELECT * FROM sprints WHERE project_id = ? ORDER BY start_date DESC');
$sprints->execute([$project_id]);
$sprintsList = $sprints->fetchAll(PDO::FETCH_ASSOC);

// Busca entradas de tempo do projeto
$time_entries = $pdo->prepare(
    'SELECT te.*, u.nome as user_name 
     FROM time_entries te 
     JOIN users u ON u.id = te.user_id
     WHERE te.project_id = ? AND te.end_time IS NOT NULL 
     ORDER BY te.end_time DESC LIMIT 20'
);
$time_entries->execute([$project_id]);
$entriesList = $time_entries->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- Coluna de Detalhes e Sprints -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Detalhes do Projeto -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900"><?= h($p['nome']) ?></h2>
                <p class="text-sm text-gray-500">Origem: <?= h($p['origem']) ?></p>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-700"><?= nl2br(h($p['descricao'])) ?></p>
            </div>
        </div>

        <!-- Sprints -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Sprints do Projeto</h2>
            </div>
            
            <!-- Formulário Nova Sprint (Sua funcionalidade pedida!) -->
            <form action="project_view.php?id=<?= $project_id ?>" method="POST" class="p-6 border-b border-gray-200 bg-gray-50">
                <input type="hidden" name="action" value="create_sprint">
                <h3 class="text-lg font-medium text-gray-800 mb-3">Criar Nova Sprint</h3>
                <?php if (!empty($error)): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded mb-4" role="alert">
                        <?= h($error) ?>
                    </div>
                <?php endif; ?>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="duracao_days" class="block text-sm font-medium text-gray-700">Duração</label>
                        <select name="duracao_days" id="duracao_days" class="mt-1 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                            <option value="7">7 dias</option>
                            <option value="15">15 dias</option>
                            <option value="30">30 dias</option>
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Data de Início</label>
                        <input type="date" name="start_date" id="start_date" value="<?= date('Y-m-d') ?>" class="mt-1 block w-full rounded-md border-0 py-2.5 px-3 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div class="sm:pt-6">
                        <button type="submit" class="w-full rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                            Criar Sprint
                        </button>
                    </div>
                </div>
            </form>

            <!-- Lista de Sprints -->
            <?php if (empty($sprintsList)): ?>
                <p class="text-sm text-gray-600 p-6">Nenhuma sprint criada para este projeto.</p>
            <?php else: ?>
                <ul role="list" class="divide-y divide-gray-100">
                    <?php foreach ($sprintsList as $s): ?>
                        <li class="px-6 py-4">
                            <p class="text-base font-semibold text-gray-900">Sprint de <?= h($s['duracao_days']) ?> dias</p>
                            <p class="text-sm text-gray-500">
                                Início: <?= h(date('d/m/Y', strtotime($s['start_date']))) ?> | 
                                Fim: <?= h(date('d/m/Y', strtotime($s['end_date']))) ?>
                            </p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna de Atividades -->
    <div class="lg:col-span-1">
        <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Atividades Recentes</h2>
            </div>
            <?php if (empty($entriesList)): ?>
                <p class="text-sm text-gray-600 p-6">Nenhum tempo monitorado para este projeto.</p>
            <?php else: ?>
                <ul role="list" class="divide-y divide-gray-100">
                    <?php foreach ($entriesList as $entry): ?>
                        <li class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-900"><?= h($entry['user_name']) ?></p>
                            <p class="text-sm text-gray-600">
                                Duração: <span class="font-medium"><?= h(format_seconds($entry['duration_seconds'])) ?></span>
                            </p>
                            <p class="text-xs text-gray-400">
                                Em: <?= h(date('d/m/Y H:i', strtotime($entry['end_time']))) ?>
                            </p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once __DIR__ . '/_footer.php';
?>