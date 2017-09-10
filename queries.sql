/*Заполнение таблицы users*/
INSERT INTO user (email, name, password, avatar) 
VALUES('ignat.v@gmail.com', 'Игнат', '$2y$10$OqvsKHQwr0Wk6FMZDoHo1uHoXd4UdxJG/5UDtUiie00XaxMHrW8ka', NULL),
('kitty_93@li.ru', 'Леночка', '$2y$10$bWtSjUhwgggtxrnJ7rxmIe63ABubHQs0AS0hgnOo41IEdMHkYoSVa', NULL),
('warrior07@mail.ru', 'Руслан', '$2y$10$2OxpEH7narYpkOT1H5cApezuzh10tZEEQ2axgFOaKW.55LxIJBgWW', NULL);

/*Заполнение таблицы projects*/
INSERT INTO projects (name, user_id) 
VALUES('Входящие','1'), ('Учеба','1'), ('Работа','1'), ('Домашние дела','1'), ('Авто','1');

/*Заполнение таблицы tasks*/
INSERT INTO tasks (user_id, project_id, complete, deadline, name) 
VALUES('1','1', '0', '2018.04.22', 'Встреча с другом'),
('1', '2', '1', '2018.04.21', 'Сделать задание первого раздела'),
('1', '3', '0', '2018.06.01', 'Собеседование в IT компании'),
('1', '3', '0', '2018.05.25', 'Выполнить тестовое задание'),
('1', '4', '0', NULL, 'Купить корм для кота'),
('1', '4', '0', NULL, 'Заказать пиццу');

/*получить список из всех проектов для одного пользователя*/
SELECT * FROM tasks WHERE user_id=1;
/*получить список из всех задач для одного проекта*/
SELECT * FROM tasks WHERE project_id=3;
/*пометить задачу как выполненную*/
UPDATE tasks SET complete=1 WHERE id=37;
/*получить все задачи для завтрашнего дня*/
SELECT * FROM tasks WHERE deadline > CURDATE();
/*обновить название задачи по её идентификатору*/
UPDATE tasks SET name='Новая задача' WHERE id=37;

