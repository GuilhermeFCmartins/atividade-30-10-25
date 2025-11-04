// This file contains JavaScript code for handling user interactions, such as drag-and-drop functionality, form submissions for adding and editing tasks, and AJAX requests to kanban_actions.php.

document.addEventListener('DOMContentLoaded', function() {
    // Drag & Drop handlers
    function onDragStart(ev) {
        ev.dataTransfer.setData('text/plain', ev.target.dataset.id);
    }

    function allowDrop(ev) {
        ev.preventDefault();
        ev.currentTarget.classList.add('dragover');
    }

    function onDrop(ev, newEstado) {
        ev.preventDefault();
        ev.currentTarget.classList.remove('dragover');
        const id = ev.dataTransfer.getData('text/plain');
        if (!id) return;

        // enviar alteração via fetch
        fetch('src/kanban_actions.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ action: 'move', tarefas_id: id, estado: newEstado })
        }).then(r => r.json()).then(resp => {
            if (resp.success) {
                const card = document.querySelector(`[data-id="${id}"]`);
                const column = document.querySelector(`.kanban-column[data-estado="${newEstado}"]`);
                column.prepend(card);
            } else {
                alert('Erro ao mover a tarefa');
            }
        }).catch(() => alert('Erro de rede'));
    }

    // adicionar
    function submitAdd(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);
        fetch('src/kanban_actions.php', { method: 'POST', body: data })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    // Adicionar a nova tarefa ao Kanban
                    const newCard = document.createElement('div');
                    newCard.className = 'kanban-card';
                    newCard.dataset.id = resp.tarefa_id;
                    newCard.innerHTML = `<h5>${resp.tarefa_nome}</h5>`;
                    document.querySelector(`.kanban-column[data-estado="pendente"]`).prepend(newCard);
                    $('#addModal').modal('hide');
                } else {
                    alert('Erro ao adicionar a tarefa');
                }
            })
            .catch(() => alert('Erro de rede'));
    }

    // abrir modal editar (carregar dados)
    function openEditModal(id) {
        fetch('src/kanban_actions.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'get', tarefas_id: id })
        }).then(r => r.json()).then(resp => {
            if (resp.success) {
                // Preencher o formulário de edição com os dados da tarefa
                document.querySelector('#formEdit [name="tarefas_id"]').value = resp.tarefa_id;
                document.querySelector('#formEdit [name="tarefa_nome"]').value = resp.tarefa_nome;
                $('#editModal').modal('show');
            } else {
                alert('Erro ao carregar a tarefa');
            }
        }).catch(() => alert('Erro de rede'));
    }

    function submitEdit(e) {
        e.preventDefault();
        const form = e.target;
        const data = new FormData(form);
        fetch('src/kanban_actions.php', { method: 'POST', body: data })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    const card = document.querySelector(`[data-id="${resp.tarefa_id}"]`);
                    card.querySelector('h5').textContent = resp.tarefa_nome;
                    $('#editModal').modal('hide');
                } else {
                    alert('Erro ao editar a tarefa');
                }
            })
            .catch(() => alert('Erro de rede'));
    }

    function deleteTask(id) {
        if (!confirm('Excluir tarefa?')) return;
        fetch('src/kanban_actions.php', {
            method: 'POST',
            body: new URLSearchParams({ action: 'delete', tarefas_id: id })
        }).then(r => r.json()).then(resp => {
            if (resp.success) {
                const card = document.querySelector(`[data-id="${id}"]`);
                card.remove();
            } else {
                alert('Erro ao excluir a tarefa');
            }
        }).catch(() => alert('Erro de rede'));
    }

    // Event listeners for forms
    document.querySelector('#formAdd').addEventListener('submit', submitAdd);
    document.querySelector('#formEdit').addEventListener('submit', submitEdit);
});