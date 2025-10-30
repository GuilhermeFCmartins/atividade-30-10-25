-- seed.sql
-- Script para popular o banco `tasks_db` com alguns usuários e tarefas de exemplo
-- Execute este arquivo no MySQL (phpMyAdmin, MySQL CLI, ou similar) após criar o schema em `bd.sql`.

USE `tasks_db`;

-- Inserir usuários de exemplo
INSERT INTO `usuario` (`nome`, `email`, `senha`) VALUES
  ('Ana Silva', 'ana@example.com', 'senha123'),
  ('Bruno Costa', 'bruno@example.com', 'senha123'),
  ('Carla Souza', 'carla@example.com', 'senha123');

-- Inserir tarefas de exemplo (usuario_id refere-se aos usuários acima)
INSERT INTO `tarefas` (`usuario_id`, `descricao`, `estado`, `prioridade`, `criado_em`) VALUES
  (1, 'Preparar relatório mensal', 'pendente', 'Alta', '2025-10-01 09:00:00'),
  (1, 'Atualizar planilha de custos', 'em andamento', 'Média', '2025-10-05 10:30:00'),
  (2, 'Revisar código do projeto X', 'em andamento', 'Alta', '2025-10-10 14:20:00'),
  (2, 'Enviar e-mail para cliente', 'concluída', 'Baixa', '2025-10-12 11:00:00'),
  (3, 'Planejar reunião de equipe', 'pendente', 'Média', '2025-10-15 16:00:00');

-- Confirme inserções (opcional):
-- SELECT * FROM usuario;
-- SELECT * FROM tarefas;
