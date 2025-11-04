<?php
require_once 'bd.php';

// Carregar usuários
$usuarios = [];
$resUsuarios = $conn->query("SELECT usuario_id, nome FROM usuario ORDER BY nome");
while ($row = $resUsuarios->fetch_assoc()) {
  $usuarios[$row['usuario_id']] = $row['nome'];
}

// Carregar tarefas por estado
$estados = ['pendente', 'em andamento', 'concluída'];
$tarefasPorEstado = [];
$stmt = $conn->prepare("SELECT t.*, u.nome as usuario_nome FROM tarefas t JOIN usuario u ON t.usuario_id = u.usuario_id ORDER BY t.criado_em DESC");
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
  $tarefasPorEstado[$row['estado']][] = $row;
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kanban - Tarefas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    .kanban-column {
      min-height: 300px;
      border: 1px dashed #ddd;
      padding: 10px;
      border-radius: 6px;
      background: #f8f9fa;
    }

    .kanban-card {
      cursor: grab;
      margin-bottom: 10px;
    }

    .kanban-column.dragover {
      background: #e9f7ef;
      border-color: #4caf50;
    }

    .card-priority-Baixa {
      border-left: 4px solid #28a745;
    }

    .card-priority-Média {
      border-left: 4px solid #ffc107;
    }

    .card-priority-Alta {
      border-left: 4px solid #dc3545;
    }
  </style>
</head>

<body>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1>Kanban de Tarefas</h1>
      <div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Nova tarefa</button>
      </div>
    </div>

    <div class="row g-3">
      <?php foreach ($estados as $estado): ?>
        <div class="col-12 col-md-4">
          <h5 class="mb-2 text-capitalize"><?= htmlspecialchars($estado) ?></h5>
          <div class="kanban-column" data-estado="<?= $estado ?>" ondragover="allowDrop(event)" ondrop="onDrop(event, '<?= $estado ?>')">
            <?php if (!empty($tarefasPorEstado[$estado])): ?>
              <?php foreach ($tarefasPorEstado[$estado] as $t): ?>
                <div class="card kanban-card card-priority-<?= htmlspecialchars($t['prioridade']) ?>" draggable="true"
                  ondragstart="onDragStart(event)" data-id="<?= $t['tarefas_id'] ?>"
                  data-usuario="<?= $t['usuario_id'] ?>"
                  data-prioridade="<?= htmlspecialchars($t['prioridade']) ?>"
                  data-criado="<?= date('Y-m-d', strtotime($t['criado_em'])) ?>">
                  <div class="card-body p-2">
                    <div class="d-flex justify-content-between">
                      <strong class="me-2"><?= htmlspecialchars(substr($t['descricao'], 0, 60)) ?></strong>
                      <small class="text-muted"><?= htmlspecialchars($t['prioridade']) ?></small>
                    </div>
                    <div class="small text-muted">
                      <?= htmlspecialchars($t['usuario_nome']) ?> • <?= htmlspecialchars(date('d/m/Y', strtotime($t['criado_em']))) ?>
                    </div>
                    <div class="mt-2 d-flex justify-content-end gap-1">
                      <button class="btn btn-sm btn-outline-primary" onclick="openEditModal(<?= $t['tarefas_id'] ?>)">Editar</button>
                      <button class="btn btn-sm btn-outline-danger" onclick="deleteTask(<?= $t['tarefas_id'] ?>)">Excluir</button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Modal Adicionar -->
  <div class="modal fade" id="addModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formAdd" class="modal-content" onsubmit="submitAdd(event)">
        <div class="modal-header">
          <h5 class="modal-title">Adicionar tarefa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="add">
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
            <textarea class="form-control" name="descricao" required></textarea>
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
              <option value="Média" selected>Média</option>
              <option value="Alta">Alta</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Data</label>
            <input type="date" class="form-control" name="criado_em" required value="<?= date('Y-m-d') ?>">
          </div>
          <div id="addError" class="text-danger small"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-success" type="submit">Salvar</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Modal Editar -->
  <div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <form id="formEdit" class="modal-content" onsubmit="submitEdit(event)">
        <div class="modal-header">
          <h5 class="modal-title">Editar tarefa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="action" value="edit">
          <input type="hidden" name="tarefas_id" id="edit_tarefas_id">
          <div class="mb-2">
            <label class="form-label">Usuário</label>
            <select class="form-select" name="usuario_id" id="edit_usuario_id" required>
              <option value="">Selecione</option>
              <?php foreach ($usuarios as $id => $nome): ?>
                <option value="<?= $id ?>"><?= htmlspecialchars($nome) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Descrição</label>
            <textarea class="form-control" name="descricao" id="edit_descricao" required></textarea>
          </div>
          <div class="mb-2">
            <label class="form-label">Status</label>
            <select class="form-select" name="estado" id="edit_estado" required>
              <option value="pendente">Pendente</option>
              <option value="em andamento">Em andamento</option>
              <option value="concluída">Finalizada</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Importância</label>
            <select class="form-select" name="prioridade" id="edit_prioridade" required>
              <option value="Baixa">Baixa</option>
              <option value="Média">Média</option>
              <option value="Alta">Alta</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Data</label>
            <input type="date" class="form-control" name="criado_em" id="edit_criado_em" required>
          </div>
          <div id="editError" class="text-danger small"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button class="btn btn-primary" type="submit">Salvar alterações</button>
        </div>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Cat Facts API function
    async function getCatFact() {
      try {
        const response = await fetch('https://catfact.ninja/fact');
        const data = await response.json();
        return data.fact;
      } catch (error) {
        return 'Miau! Não consegui buscar um fato sobre gatos agora.';
      }
    }

    // Função para mostrar notificação com fato sobre gatos
    async function showCatNotification(title, icon = 'success') {
      const catFact = await getCatFact();
      await Swal.fire({
        title: title,
        text: catFact,
        icon: icon,
        confirmButtonText: 'Legal!',
        confirmButtonColor: '#28a745'
      });
    }

    // Drag & Drop handlers
    function onDragStart(ev) {
      ev.dataTransfer.setData('text/plain', ev.target.dataset.id);
      ev.target.classList.add('dragging');
    }

    function allowDrop(ev) {
      ev.preventDefault();
      ev.currentTarget.classList.add('dragover');
    }

    async function onDrop(ev, newEstado) {
      ev.preventDefault();
      ev.currentTarget.classList.remove('dragover');
      const id = ev.dataTransfer.getData('text/plain');
      if (!id) return;

      try {
        const response = await fetch('kanban_actions.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
            action: 'move',
            tarefas_id: id,
            estado: newEstado
          })
        });

        const resp = await response.json();
        if (resp.success) {
          const card = document.querySelector(`[data-id='${id}']`);
          const column = document.querySelector(`.kanban-column[data-estado='${newEstado}']`);
          if (card && column) {
            column.prepend(card);
            await showCatNotification('Tarefa movida com sucesso!');
          }
        } else {
          throw new Error(resp.message || 'Erro ao mover tarefa');
        }
      } catch (error) {
        Swal.fire({
          title: 'Erro!',
          text: error.message,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    }

    // Adicionar tarefa
    async function submitAdd(e) {
      e.preventDefault();
      const form = e.target;
      const data = new FormData(form);

      try {
        const response = await fetch('kanban_actions.php', {
          method: 'POST',
          body: data
        });
        const resp = await response.json();

        if (resp.success) {
          await showCatNotification('Tarefa adicionada com sucesso!');
          location.reload();
        } else {
          throw new Error(resp.message || 'Erro ao adicionar tarefa');
        }
      } catch (error) {
        document.getElementById('addError').innerText = error.message;
      }
    }

    // Editar tarefa
    async function openEditModal(id) {
      try {
        const response = await fetch('kanban_actions.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
          },
          body: new URLSearchParams({
            action: 'get',
            tarefas_id: id
          })
        });
        const resp = await response.json();

        if (resp.success && resp.task) {
          const t = resp.task;
          document.getElementById('edit_tarefas_id').value = t.tarefas_id;
          document.getElementById('edit_usuario_id').value = t.usuario_id;
          document.getElementById('edit_descricao').value = t.descricao;
          document.getElementById('edit_estado').value = t.estado;
          document.getElementById('edit_prioridade').value = t.prioridade;
          document.getElementById('edit_criado_em').value = t.criado_em_date;

          const modal = new bootstrap.Modal(document.getElementById('editModal'));
          modal.show();
        } else {
          throw new Error(resp.message || 'Não foi possível carregar a tarefa');
        }
      } catch (error) {
        Swal.fire({
          title: 'Erro!',
          text: error.message,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      }
    }

    async function submitEdit(e) {
      e.preventDefault();
      const form = e.target;
      const data = new FormData(form);

      try {
        const response = await fetch('kanban_actions.php', {
          method: 'POST',
          body: data
        });
        const resp = await response.json();

        if (resp.success) {
          await showCatNotification('Tarefa atualizada com sucesso!');
          location.reload();
        } else {
          throw new Error(resp.message || 'Erro ao atualizar tarefa');
        }
      } catch (error) {
        document.getElementById('editError').innerText = error.message;
      }
    }

    // Excluir tarefa
    async function deleteTask(id) {
      const result = await Swal.fire({
        title: 'Tem certeza?',
        text: 'Essa ação não pode ser desfeita!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, excluir!',
        cancelButtonText: 'Cancelar'
      });

      if (result.isConfirmed) {
        try {
          const response = await fetch('kanban_actions.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
              action: 'delete',
              tarefas_id: id
            })
          });
          const resp = await response.json();

          if (resp.success) {
            const card = document.querySelector(`[data-id='${id}']`);
            if (card) {
              card.remove();
              await showCatNotification('Tarefa excluída com sucesso!');
            }
          } else {
            throw new Error(resp.message || 'Erro ao excluir tarefa');
          }
        } catch (error) {
          Swal.fire({
            title: 'Erro!',
            text: error.message,
            icon: 'error',
            confirmButtonText: 'OK'
          });
        }
      }
    }

    // Adicionar event listeners quando o documento carregar
    document.addEventListener('DOMContentLoaded', function() {
      const formAdd = document.getElementById('formAdd');
      const formEdit = document.getElementById('formEdit');

      if (formAdd) formAdd.addEventListener('submit', submitAdd);
      if (formEdit) formEdit.addEventListener('submit', submitEdit);
    });
  </script>
</body>

</html>