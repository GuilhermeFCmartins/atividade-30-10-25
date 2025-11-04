# Kanban Task Management System

A dynamic task management system with drag-and-drop functionality, built using PHP, MySQL, Bootstrap, and enhanced with Cat Facts API integration.

## Features

- **User Authentication**
  - Login system
  - User registration
  - Secure password handling

- **Task Management**
  - Create, edit, and delete tasks
  - Drag-and-drop task status updates
  - Priority levels (Low, Medium, High)
  - Task assignment to users
  - Date tracking

- **Interactive UI**
  - Kanban board layout
  - Color-coded priority indicators
  - Responsive design
  - Fun cat facts on actions!

## Requirements

- PHP 7.4+
- MySQL/MariaDB
- Web server (XAMPP recommended)
- Modern web browser
- Internet connection (for Cat Facts API)

## Installation

1. **Setup Database**
   ```sql  
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
   ```

2. **File Setup**
   - Clone/copy files to your web server directory
   - Configure database connection in `bd.php`
   - Ensure proper file permissions

3. **Access**
   - Navigate to `login.php` in your browser
   - Register a new account
   - Start managing tasks!

## Usage

1. **Login/Register**
   - Create an account or login
   - Secure password storage

2. **Managing Tasks**
   - Click "Nova tarefa" to add
   - Drag tasks between columns
   - Edit/delete using card buttons
   - Enjoy random cat facts!

3. **Task States**
   - Pendente (Pending)
   - Em andamento (In Progress)
   - Concluída (Completed)

4. **Priority Levels**
   -  Baixa (Low)
   - Média (Medium)
   - Alta (High)

## Security Features

- Password hashing
- SQL injection prevention
- XSS protection
- Session management

## API Integration

- Uses [Cat Facts API](https://catfact.ninja/) for fun notifications
- Provides random cat facts on task actions
- Enhances user experience with playful feedback

## Responsive Design

- Works on desktop and mobile
- Bootstrap 5 framework
- Clean, modern interface
- Intuitive drag-and-drop

## Known Issues

- Cat Facts API may have rate limits
- Drag-and-drop on mobile needs improvement
- Session timeout not configurable

## Future Updates

- [ ] Dark mode
- [ ] Task filtering
- [ ] User avatars
- [ ] Activity log
- [ ] Task comments

