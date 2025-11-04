<?php
require_once 'bd.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => 'Ação inválida'];

if (!isset($_POST['action'])) {
    echo json_encode($response);
    exit;
}

switch ($_POST['action']) {
    case 'add':
        if (isset($_POST['usuario_id'], $_POST['descricao'], $_POST['estado'], $_POST['prioridade'], $_POST['criado_em'])) {
            $stmt = $conn->prepare("INSERT INTO tarefas (usuario_id, descricao, estado, prioridade, criado_em) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "issss",
                $_POST['usuario_id'],
                $_POST['descricao'],
                $_POST['estado'],
                $_POST['prioridade'],
                $_POST['criado_em']
            );

            if ($stmt->execute()) {
                $response = ['success' => true];
            } else {
                $response['message'] = 'Erro ao adicionar tarefa';
            }
        }
        break;

    case 'edit':
        if (isset($_POST['tarefas_id'], $_POST['usuario_id'], $_POST['descricao'], $_POST['estado'], $_POST['prioridade'], $_POST['criado_em'])) {
            $stmt = $conn->prepare("UPDATE tarefas SET usuario_id=?, descricao=?, estado=?, prioridade=?, criado_em=? WHERE tarefas_id=?");
            $stmt->bind_param(
                "issssi",
                $_POST['usuario_id'],
                $_POST['descricao'],
                $_POST['estado'],
                $_POST['prioridade'],
                $_POST['criado_em'],
                $_POST['tarefas_id']
            );

            if ($stmt->execute()) {
                $response = ['success' => true];
            } else {
                $response['message'] = 'Erro ao atualizar tarefa';
            }
        }
        break;

    case 'delete':
        if (isset($_POST['tarefas_id'])) {
            $stmt = $conn->prepare("DELETE FROM tarefas WHERE tarefas_id = ?");
            $stmt->bind_param("i", $_POST['tarefas_id']);

            if ($stmt->execute()) {
                $response = ['success' => true];
            } else {
                $response['message'] = 'Erro ao excluir tarefa';
            }
        }
        break;

    case 'move':
        if (isset($_POST['tarefas_id'], $_POST['estado'])) {
            $stmt = $conn->prepare("UPDATE tarefas SET estado = ? WHERE tarefas_id = ?");
            $stmt->bind_param("si", $_POST['estado'], $_POST['tarefas_id']);

            if ($stmt->execute()) {
                $response = ['success' => true];
            } else {
                $response['message'] = 'Erro ao mover tarefa';
            }
        }
        break;

    case 'get':
        if (isset($_POST['tarefas_id'])) {
            $stmt = $conn->prepare("SELECT * FROM tarefas WHERE tarefas_id = ?");
            $stmt->bind_param("i", $_POST['tarefas_id']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($task = $result->fetch_assoc()) {
                $task['criado_em_date'] = date('Y-m-d', strtotime($task['criado_em']));
                $response = ['success' => true, 'task' => $task];
            } else {
                $response['message'] = 'Tarefa não encontrada';
            }
        }
        break;
}

echo json_encode($response);
