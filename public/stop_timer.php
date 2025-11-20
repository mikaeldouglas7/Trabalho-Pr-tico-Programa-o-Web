<?php
// sprint_manager/public/stop_timer.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';
ensure_logged_in();

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$entry_id = intval($_POST['entry_id'] ?? 0);

if (!$entry_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID da entrada não fornecido.']);
    exit;
}

try {
    // 1. Encontra o timer
    $stmt = $pdo->prepare('SELECT * FROM time_entries WHERE id = ? AND user_id = ? AND end_time IS NULL LIMIT 1');
    $stmt->execute([$entry_id, $user_id]);
    $entry = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$entry) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Timer não encontrado ou já finalizado.']);
        exit;
    }

    // 2. Para o timer e calcula a duração
    $stmt = $pdo->prepare('UPDATE time_entries SET end_time = NOW(), duration_seconds = TIMESTAMPDIFF(SECOND, start_time, NOW()) WHERE id = ?');
    $stmt->execute([$entry_id]);

    // 3. Pega a duração real
    $dur = $pdo->prepare('SELECT duration_seconds FROM time_entries WHERE id = ?');
    $dur->execute([$entry_id]);
    $duration = (int)$dur->fetchColumn();

    // 4. Atualiza o "ponto" (work_days)
    $date_work = date('Y-m-d', strtotime($entry['start_time']));
    $stmt = $pdo->prepare(
        'INSERT INTO work_days (user_id, date_work, total_seconds) 
         VALUES (?, ?, ?) 
         ON DUPLICATE KEY UPDATE total_seconds = total_seconds + VALUES(total_seconds)'
    );
    $stmt->execute([$user_id, $date_work, $duration]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro de banco de dados: ' . $e->getMessage()]);
}