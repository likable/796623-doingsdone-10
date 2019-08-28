INSERT INTO users 
(email, name, password)
VALUES
("user1@ya.ru", "user1", "user1password"), 
("user2@ya.ru", "user2", "user2password"), 
("user3@ya.ru", "user3", "user3password");

INSERT INTO projects
(title, author_id)
VALUES
("Входящие", 1), 
("Учеба", 1), 
("Работа", 1),
("Домашние дела", 1),
("Авто", 1);

INSERT INTO tasks
(task_title, task_expiration, status, author_id, project_id)
VALUES
("Собеседование в IT компании", 
"01.12.2019", 
0,
1,
3), 
("Выполнить тестовое задание", 
"25.12.2019", 
0,
1,
3),
("Сделать задание первого раздела", 
"21.12.2019", 
1,
1,
2), 
("Встреча с другом", 
"22.12.2019", 
0,
1,
1), 
("Купить корм для кота", 
"28.08.2019", 
0,
1,
4), 
("Заказать пиццу", 
, 
0,
1,
4);

#получить список из всех проектов для одного пользователя
SELECT title FROM projects WHERE author_id = 1;

#получить список из всех задач для одного проекта
SELECT task_title FROM tasks WHERE project_id = 4;

#пометить задачу как выполненную
UPDATE tasks SET status = 1 WHERE id = 1;

#обновить название задачи по её идентификатору
UPDATE tasks SET task_title = "Заказать суши" WHERE id = 6;
