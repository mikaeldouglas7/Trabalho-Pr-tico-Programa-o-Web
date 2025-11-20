<?php
/**
 * public/install.php
 * Roda migrations/schema.sql e cria admin com senha admin123.
 * RODE UMA VEZ e depois delete este arquivo.
 */
$DB_HOST = "127.0.0.1";
$DB_PORT = "8889"; // Porta do MAMP
$DB_USER = "root";
$DB_PASS = "root";
$DB_NAME = "sprint_manager";
$schemaFile = __DIR__ . '/../migrations/schema.sql';

echo "<pre style='font-family: monospace; line-height: 1.5; padding: 20px; background: #f4f4f4; border: 1px solid #ccc; border-radius: 8px;'>";
try {
    // Conecta ao MySQL (sem dbname) para criar o banco
    $pdo = new PDO("mysql:host={$DB_HOST};port={$DB_PORT}", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✔ Conectado ao MySQL com sucesso (Porta: $DB_PORT).\n";
} catch (PDOException $e) {
    die("Erro conexão: " . $e->getMessage());
}

// cria o banco se não existir e usa
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$DB_NAME` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;");
    $pdo->exec("USE `$DB_NAME`");
    echo "✔ Banco de dados '$DB_NAME' verificado/criado e selecionado.\n";
} catch (PDOException $e) {
    die("Erro ao criar/selecionar banco: " . $e->getMessage());
}


// roda schema.sql
if (!file_exists($schemaFile)) {
    die("Erro: Arquivo migrations/schema.sql não encontrado.");
}
try {
    $schema = file_get_contents($schemaFile);
    // Remove o "USE sprint_manager;" do arquivo SQL, pois já fizemos isso
    $schema = preg_replace('/USE .*/i', '', $schema);
    $pdo->exec($schema);
    echo "✔ Tabelas criadas com sucesso (users, projects, sprints, etc.).\n";
} catch (PDOException $e) {
    die("Erro ao executar schema.sql: " . $e->getMessage());
}


// cria admin
try {
    $hash = password_hash('admin123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (nome, cpf, password_hash, is_admin) VALUES ('Admin', '00000000000', ?, 1);");
    $stmt->execute([$hash]);
    echo "✔ Usuário Admin criado/verificado.\n";
} catch (PDOException $e) {
    die("Erro ao criar admin: " . $e->getMessage());
}

echo "\n<h2 style='color: green;'>✔ Instalação concluída com sucesso!</h2>";
echo "<p>Usuário Admin: CPF = <strong>00000000000</strong> / Senha = <strong>admin123</strong></p>";
echo "<p style='color:red; font-weight:bold;'>Delete este arquivo (<code>public/install.php</code>) agora por segurança.</p>";
echo "<p><a href='login.php' style='display:inline-block; padding: 10px 15px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Ir para a página de Login</a></p>";
echo "</pre>";