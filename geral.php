<?php
require_once 'bd.php';
// Carregar usuários
$usuarios = [];
$resUsuarios = $conn->query("SELECT usuario_id, nome FROM usuario ORDER BY nome");
while ($row = $resUsuarios->fetch_assoc()) {
    $usuarios[$row['usuario_id']] = $row['nome'];
}
// Carregar tarefas
$tarefas = [];
$resTarefas = $conn->query("SELECT t.*, u.nome as usuario_nome FROM tarefas t JOIN usuario u ON t.usuario_id = u.usuario_id ORDER BY t.tarefas_id DESC");
while ($row = $resTarefas->fetch_assoc()) {
    $tarefas[] = $row;
}
// Adicionar tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $usuario_id = intval($_POST['usuario_id']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $estado = $conn->real_escape_string($_POST['estado']);
    $prioridade = $conn->real_escape_string($_POST['prioridade']);
    $criado_em = $conn->real_escape_string($_POST['criado_em']);
    $conn->query("INSERT INTO tarefas (usuario_id, descricao, estado, prioridade, criado_em) VALUES ($usuario_id, '$descricao', '$estado', '$prioridade', '$criado_em')");
    header('Location: geral.php');
    exit;
}
// Editar tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit'])) {
    $tarefas_id = intval($_POST['tarefas_id']);
    $usuario_id = intval($_POST['usuario_id']);
    $descricao = $conn->real_escape_string($_POST['descricao']);
    $estado = $conn->real_escape_string($_POST['estado']);
    $prioridade = $conn->real_escape_string($_POST['prioridade']);
    $criado_em = $conn->real_escape_string($_POST['criado_em']);
    $conn->query("UPDATE tarefas SET usuario_id=$usuario_id, descricao='$descricao', estado='$estado', prioridade='$prioridade', criado_em='$criado_em' WHERE tarefas_id=$tarefas_id");
    header('Location: geral.php');
    exit;
}
?>
<!doctype html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dupla de Tabelas - Adicionar e Editar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="container py-4">
    <h1 class="mb-4">Tarefas: adicionar e editar</h1>
    <ul class="nav nav-tabs mb-3" id="tabTarefas" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-add" data-bs-toggle="tab" data-bs-target="#pane-add" type="button" role="tab" aria-controls="pane-add" aria-selected="true">Adicionar</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-edit" data-bs-toggle="tab" data-bs-target="#pane-edit" type="button" role="tab" aria-controls="pane-edit" aria-selected="false">Editar</button>
      </li>
    </ul>
    <div class="tab-content" id="tabContentTarefas">
      <div class="tab-pane fade show active" id="pane-add" role="tabpanel" aria-labelledby="tab-add">
        <div class="card mb-4">
          <div class="card-header d-flex justify-content-between align-items-center">
            <strong>Tarefas (Adicionar)</strong>
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">Adicionar tarefa</button>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Status</th>
                    <th>Importância</th>
                    <th>Data</th>
                    <th>Descrição</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($tarefas as $tarefa): ?>
                  <tr>
                    <td><?= $tarefa['tarefas_id'] ?></td>
                    <td><?= htmlspecialchars($tarefa['usuario_nome']) ?></td>
                    <td><?= htmlspecialchars($tarefa['estado']) ?></td>
                    <td><?= htmlspecialchars($tarefa['prioridade']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($tarefa['criado_em']))) ?></td>
                    <td><?= htmlspecialchars($tarefa['descricao']) ?></td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="tab-pane fade" id="pane-edit" role="tabpanel" aria-labelledby="tab-edit">
        <div class="card mb-4">
          <div class="card-header">
            <strong>Tarefas (Editar)</strong>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead class="table-light">
                  <tr>
                    <th>ID</th>
                    <th>Usuário</th>
                    <th>Status</th>
                    <th>Importância</th>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($tarefas as $tarefa): ?>
                  <tr>
                    <td><?= $tarefa['tarefas_id'] ?></td>
                    <td><?= htmlspecialchars($tarefa['usuario_nome']) ?></td>
                    <td><?= htmlspecialchars($tarefa['estado']) ?></td>
                    <td><?= htmlspecialchars($tarefa['prioridade']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($tarefa['criado_em']))) ?></td>
                    <td><?= htmlspecialchars($tarefa['descricao']) ?></td>
                    <td>
                      <button class="btn btn-primary btn-sm btn-edit" 
                        data-id="<?= $tarefa['tarefas_id'] ?>"
                        data-usuario="<?= $tarefa['usuario_id'] ?>"
                        data-descricao="<?= htmlspecialchars($tarefa['descricao']) ?>"
                        data-estado="<?= $tarefa['estado'] ?>"
                        data-prioridade="<?= $tarefa['prioridade'] ?>"
                        data-criado_em="<?= date('Y-m-d', strtotime($tarefa['criado_em'])) ?>"
                        >Editar</button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Modal Adicionar -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post">
            <div class="modal-header">
              <h5 class="modal-title">Adicionar tarefa</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="add" value="1">
              <div class="mb-2">
                <label class="form-label">Usuário</label>
                <select class="form-select" name="usuario_id" required>
                  <option value="">Selecione</option>
                  <?php foreach ($usuarios as $id => $nome): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($nome) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Descrição</label>
                <input class="form-control" name="descricao" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="estado" required>
                  <option value="pendente">Pendente</option>
                  <option value="em andamento">Em andamento</option>
                  <option value="concluída">Finalizada</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Importância</label>
                <select class="form-select" name="prioridade" required>
                  <option value="Baixa">Baixa</option>
                  <option value="Média">Média</option>
                  <option value="Alta">Alta</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Data</label>
                <input type="date" class="form-control" name="criado_em" required value="<?= date('Y-m-d') ?>">
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-success">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- Modal Editar -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="post">
            <div class="modal-header">
              <h5 class="modal-title">Editar tarefa</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" name="edit" value="1">
              <input type="hidden" name="tarefas_id" id="editTarefaId">
              <div class="mb-2">
                <label class="form-label">Usuário</label>
                <select class="form-select" name="usuario_id" id="editUsuario" required>
                  <option value="">Selecione</option>
                  <?php foreach ($usuarios as $id => $nome): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($nome) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Descrição</label>
                <input class="form-control" name="descricao" id="editDescricao" required>
              </div>
              <div class="mb-2">
                <label class="form-label">Status</label>
                <select class="form-select" name="estado" id="editEstado" required>
                  <option value="pendente">Pendente</option>
                  <option value="em andamento">Em andamento</option>
                  <option value="concluída">Finalizada</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Importância</label>
                <select class="form-select" name="prioridade" id="editPrioridade" required>
                  <option value="Baixa">Baixa</option>
                  <option value="Média">Média</option>
                  <option value="Alta">Alta</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label">Data</label>
                <input type="date" class="form-control" name="criado_em" id="editCriadoEm" required>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Salvar alterações</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Preencher modal de edição
    document.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', function() {
        document.getElementById('editTarefaId').value = this.dataset.id;
        document.getElementById('editUsuario').value = this.dataset.usuario;
        document.getElementById('editDescricao').value = this.dataset.descricao;
        document.getElementById('editEstado').value = this.dataset.estado;
        document.getElementById('editPrioridade').value = this.dataset.prioridade;
        document.getElementById('editCriadoEm').value = this.dataset.criado_em;
        var modal = new bootstrap.Modal(document.getElementById('editModal'));
        modal.show();
      });
    });
  </script>
</body>
</html>