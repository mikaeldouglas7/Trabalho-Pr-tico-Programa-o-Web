<?php
// sprint_manager/public/dashboard.php
require_once __DIR__ . '/../src/db.php';
$page_title = 'Dashboard'; // Define o título para o _header.php
require_once __DIR__ . '/_header.php'; // Inclui o header (já chama ensure_logged_in())

$user_id = $_SESSION['user_id'];

// Busca projetos
$projects = $pdo->query('SELECT * FROM projects ORDER BY created_at DESC')->fetchAll(PDO::FETCH_ASSOC);

// Busca a entrada de tempo ativa (timer rodando)
$stmt = $pdo->prepare(
    'SELECT te.*, p.nome as project_name 
     FROM time_entries te 
     JOIN projects p ON p.id = te.project_id 
     WHERE te.user_id = ? AND te.end_time IS NULL 
     LIMIT 1'
);
$stmt->execute([$user_id]);
$activeEntry = $stmt->fetch(PDO::FETCH_ASSOC);

// Busca últimas 5 entradas de tempo finalizadas
$stmt = $pdo->prepare(
    'SELECT te.*, p.nome as project_name 
     FROM time_entries te 
     JOIN projects p ON p.id = te.project_id 
     WHERE te.user_id = ? AND te.end_time IS NOT NULL 
     ORDER BY te.end_time DESC
     LIMIT 5'
);
$stmt->execute([$user_id]);
$recentEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!-- Card do Timer Ativo -->
<?php if ($activeEntry): ?>
<div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6 border-l-4 border-indigo-500">
    <div class="p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-2">Atividade em Andamento</h2>
        <div class="border-t border-gray-200 pt-4">
            <dl class="divide-y divide-gray-100">
                <div class="px-0 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Projeto</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0 font-semibold"><?= h($activeEntry['project_name']) ?></dd>
                </div>
                <div class="px-0 py-3 sm:grid sm:grid-cols-3 sm:gap-4">
                    <dt class="text-sm font-medium leading-6 text-gray-900">Iniciado em</dt>
                    <dd class="mt-1 text-sm leading-6 text-gray-700 sm:col-span-2 sm:mt-0"><?= h(date('d/m/Y H:i:s', strtotime($activeEntry['start_time']))) ?></dd>
                </div>
            </dl>
            <!-- Botão de Parar (Note a classe 'btn-stop') -->
            <button 
                onclick="stopTimer(<?= $activeEntry['id'] ?>)" 
                class="btn-stop mt-4 w-full rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                Parar Timer
            </button>
        </div>
    </div>
</div>
<?php endif; ?>


<!-- Card de Iniciar Novo Timer -->
<div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
    <div class="px-6 py-5">
        <h2 class="text-xl font-semibold text-gray-900">Iniciar Timer em um Projeto</h2>
    </div>
    <div class="border-t border-gray-200 p-6">
        <?php if ($activeEntry): ?>
            <!-- Mensagem de bloqueio se o timer estiver ativo -->
            <p class="text-sm text-gray-600">Você não pode iniciar um novo timer, pois já existe uma atividade em andamento. Pare a atividade atual primeiro.</p>
        <?php elseif (empty($projects)): ?>
            <p class="text-sm text-gray-600">Você ainda não tem projetos. <a href="projects.php" class="text-indigo-600 hover:text-indigo-500">Crie seu primeiro projeto</a> para começar a monitorar o tempo.</p>
        <?php else: ?>
            <!-- Lista de projetos para iniciar -->
            <ul role="list" class="divide-y divide-gray-100">
                <?php foreach ($projects as $p): ?>
                    <li class="flex items-center justify-between gap-x-6 py-4">
                        <div>
                            <p class="text-base font-semibold leading-6 text-gray-900"><?= h($p['nome']) ?></p>
                            <p class="mt-1 text-xs leading-5 text-gray-500">Origem: <?= h($p['origem'] ?? 'Não definida') ?></p>
                        </div>
                        <!-- Botão de Iniciar (Note a classe 'btn-start') -->
                        <button 
                            onclick="startTimer(<?= $p['id'] ?>)"
                            class="btn-start rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            Iniciar
                        </button>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<!-- Card de Atividades Recentes -->
<div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <div class="px-6 py-5">
        <h2 class="text-xl font-semibold text-gray-900">Suas Atividades Recentes</h2>
    </div>
    <div class="border-t border-gray-200">
        <?php if (empty($recentEntries)): ?>
            <p class="text-sm text-gray-600 p-6">Nenhuma atividade finalizada recentemente.</p>
        <?php else: ?>
            <ul role="list" class="divide-y divide-gray-100">
                <?php foreach ($recentEntries as $entry): ?>
                    <li class="flex items-center justify-between gap-x-6 px-6 py-4">
                        <div>
                            <p class="text-base font-semibold leading-6 text-gray-900"><?= h($entry['project_name']) ?></p>
                            <p class="mt-1 text-xs leading-5 text-gray-500">
                                Em: <?= h(date('d/m/Y', strtotime($entry['end_time']))) ?> | 
                                Duração: <span class="font-medium"><?= h(format_seconds($entry['duration_seconds'])) ?></span>
                            </p>
                        </div>
                        <div class="text-sm text-gray-500 text-right">
                             <p>Início: <?= h(date('H:i', strtotime($entry['start_time']))) ?></p>
                             <p>Fim: <?= h(date('H:i', strtotime($entry['end_time']))) ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php
// Inclui o footer
require_once __DIR__ . '/_footer.php'; 
?>