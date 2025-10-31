# Sistema de Gerenciamento de Tarefas

Este sistema permite cadastrar, visualizar e editar tarefas associadas a usuários, utilizando PHP, MySQL e Bootstrap.

## Funcionalidades
- Cadastro de tarefas com:
  - Usuário (selecionado do banco de dados)
  - Descrição
  - Status (pendente, em andamento, finalizada)
  - Importância (baixa, média, alta)
  - Data
- Edição de tarefas já cadastradas
- Visualização de todas as tarefas em tabelas
- Interface moderna com Bootstrap e navegação por abas

## Requisitos
- PHP 7.4+
- MySQL/MariaDB
- Servidor web (ex: XAMPP, WAMP, etc.)

## Instalação
1. Clone ou copie os arquivos para o diretório do seu servidor web.
2. Importe o banco de dados:
   - No phpMyAdmin ou via terminal, execute o script `bd.sql` para criar o banco e as tabelas.
3. Ajuste as credenciais do banco em `bd.php` se necessário.
4. Acesse `geral.php` pelo navegador (ex: http://localhost/atividade1/atividade-30-10-25-1/geral.php).

## Estrutura do Banco de Dados
- Tabela `usuario`: armazena os usuários do sistema.
- Tabela `tarefas`: armazena as tarefas, vinculadas a um usuário.

## Como Usar
- **Adicionar Tarefa:**
  - Clique na aba "Adicionar" e depois em "Adicionar tarefa".
  - Preencha os campos e salve.
- **Editar Tarefa:**
  - Clique na aba "Editar".
  - Clique em "Editar" na linha desejada, altere os dados e salve.

## Observações
- O campo ID é auto-incremento e não pode ser alterado.
- O sistema utiliza Bootstrap 5 para o layout responsivo.
- O sistema não possui autenticação de usuários.

---
Desenvolvido para atividade de sala (30/10/2025).
