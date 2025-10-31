
CREATE DATABASE IF NOT EXISTS `tasks_db`;
USE `tasks_db`;


CREATE TABLE IF NOT EXISTS `usuario` (
    `usuario_id` INT NOT NULL AUTO_INCREMENT,
    `nome` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `senha` VARCHAR(255) NOT NULL,
    PRIMARY KEY (`usuario_id`),
    UNIQUE KEY `uniq_email` (`email`)
) 

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
) 
