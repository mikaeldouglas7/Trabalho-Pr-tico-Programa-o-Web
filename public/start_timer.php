<?php
// sprint_manager/public/start_timer.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';
ensure_logged_in(); // helpers.php já inicia a sessão

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$project_id = intval($_POST['project_id'] ?? 0);

if (!$project_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID do projeto não fornecido.']);
    exit;
}

try {
    // REGRA DE NEGÓCIO: Verifica se já existe um timer ativo
    $stmt = $pdo->prepare('SELECT id FROM time_entries WHERE user_id = ? AND end_time IS NULL');
    $stmt->execute([$user_id]);
    if ($stmt->fetch()) {
        http_response_code(409); // 409 Conflict
        echo json_encode(['success' => false, 'message' => 'Você já possui um timer ativo. Pare o timer atual antes de iniciar um novo.']);
        exit;
    }

    // Inicia o novo timer
    $stmt = $pdo->prepare('INSERT INTO time_entries (user_id, project_id, start_time) VALUES (?, ?, NOW())');
    $stmt->execute([$user_id, $project_id]);
    $id = $pdo->lastInsertId();

    echo json_encode(['success' => true, 'entry_id' => $id]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro de banco de dados: ' . $e->getMessage()]);
}