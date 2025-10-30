<?php
// tasks.php
// MySQL-backed CRUD functions for tasks using the `tasks_db` schema.
// Adjust DB credentials below if needed for your environment.

define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'tasks_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');

function get_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

function get_user_by_name(string $nome): ?array
{
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT * FROM usuario WHERE nome = :nome LIMIT 1');
    $stmt->execute([':nome' => $nome]);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

function create_task_by_userid(int $user_id, string $descricao, string $estado, string $prioridade): int
{
    $pdo = get_db();
    $stmt = $pdo->prepare('INSERT INTO tarefas (usuario_id, descricao, estado, prioridade) VALUES (:usuario_id, :descricao, :estado, :prioridade)');
    $stmt->execute([
        ':usuario_id' => $user_id,
        ':descricao' => $descricao,
        ':estado' => $estado,
        ':prioridade' => $prioridade,
    ]);
    return (int)$pdo->lastInsertId();
}

function create_task_by_username(string $nome, string $descricao, string $estado, string $prioridade): array
{
    $user = get_user_by_name($nome);
    if (!$user) {
        return ['success' => false, 'error' => 'Usuário não encontrado: ' . $nome];
    }
    $id = create_task_by_userid((int)$user['usuario_id'], $descricao, $estado, $prioridade);
    return ['success' => true, 'id' => $id];
}

function get_tasks(): array
{
    $pdo = get_db();
    $sql = 'SELECT t.*, u.nome AS usuario_nome FROM tarefas t JOIN usuario u ON t.usuario_id = u.usuario_id ORDER BY t.criado_em DESC';
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll();
}

function get_task(int $id): ?array
{
    $pdo = get_db();
    $stmt = $pdo->prepare('SELECT t.*, u.nome AS usuario_nome FROM tarefas t JOIN usuario u ON t.usuario_id = u.usuario_id WHERE t.tarefas_id = :id');
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    return $row === false ? null : $row;
}

function update_task(int $id, int $user_id, string $descricao, string $estado, string $prioridade): bool
{
    $pdo = get_db();
    $stmt = $pdo->prepare('UPDATE tarefas SET usuario_id = :usuario_id, descricao = :descricao, estado = :estado, prioridade = :prioridade WHERE tarefas_id = :id');
    return $stmt->execute([
        ':usuario_id' => $user_id,
        ':descricao' => $descricao,
        ':estado' => $estado,
        ':prioridade' => $prioridade,
        ':id' => $id,
    ]);
}

function delete_task(int $id): bool
{
    $pdo = get_db();
    $stmt = $pdo->prepare('DELETE FROM tarefas WHERE tarefas_id = :id');
    return $stmt->execute([':id' => $id]);
}
