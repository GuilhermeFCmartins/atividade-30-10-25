
CREATE DATABASE IF NOT EXISTS `tasks_db` DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;
USE `tasks_db`;

-- Tabela de usuários

CREATE TABLE IF NOT EXISTS `usuario` (
    `usuario_id` INT NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`usuario_id`),
    UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabela de tarefas com chave estrangeira para usuario.usuario_id

CREATE TABLE IF NOT EXISTS `tarefas` (
    `tarefas_id` INT NOT NULL AUTO_INCREMENT,
    `usuario_id` INT NOT NULL,
    `descricao` VARCHAR(255) NOT NULL,
    `estado` ENUM('pendente', 'em andamento', 'concluída') NOT NULL DEFAULT 'pendente',
    `prioridade` ENUM('Baixa', 'Média', 'Alta') NOT NULL DEFAULT 'Média',
    `criado_em` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`tarefas_id`),
    INDEX `idx_usuario_id` (`usuario_id`),
    CONSTRAINT `fk_tarefas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario`(`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





-- Observações:
-- 1)Este arquivo cria o banco `tasks_db`, a tabela `usuario` (com PK auto_increment) e a tabela `tarefas`.
-- 2)A coluna `tarefas.usuario_id` é chave estrangeira para `usuario.usuario_id`.
-- 3)Usamos InnoDB para suportar chaves estrangeiras; se estiver usando MyISAM, mude o engine.
