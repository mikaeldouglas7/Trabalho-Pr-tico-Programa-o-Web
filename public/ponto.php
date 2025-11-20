<?php
// sprint_manager/public/ponto.php
require_once __DIR__ . '/../src/db.php';
$page_title = 'Meu Ponto'; // Define o título
require_once __DIR__ . '/_header.php'; // Inclui o header

$user_id = $_SESSION['user_id'];

// lista últimos 60 dias (work_days)
$stmt = $pdo->prepare('SELECT date_work, total_seconds FROM work_days WHERE user_id = ? ORDER BY date_work DESC LIMIT 60');
$stmt->execute([$user_id]);
$days = $stmt->fetchAll(PDO::FETCH_ASSOC);

// somar total geral
$tot = $pdo->prepare('SELECT SUM(total_seconds) FROM work_days WHERE user_id = ?');
$tot->execute([$user_id]);
$totalAll = (int)$tot->fetchColumn();
?>

<!-- Card de Total Acumulado -->
<div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
    <div class="p-6">
        <h2 class="text-base font-medium text-gray-500">Total Acumulado</h2>
        <p class="mt-1 text-4xl font-bold tracking-tight text-indigo-600">
            <?= h(format_seconds($totalAll)) ?>
        </p>
        <p class="text-sm text-gray-500">Total de horas registradas no sistema.</p>
    </div>
</div>

<!-- Tabela de Dias Trabalhados -->
<div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Seus Lançamentos Diários</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Data
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Total de Horas
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($days)): ?>
                    <tr>
                        <td colspan="2" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                            Nenhum registro de ponto encontrado. Comece a usar o timer!
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach($days as $d): ?>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                <!-- Formatando a data para o padrão BR -->
                                <?= h(date('d/m/Y', strtotime($d['date_work']))) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                <?= h(format_seconds((int)$d['total_seconds'])) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once __DIR__ . '/_footer.php';
?>