<section class="content__side">
    <h2 class="content__side-heading">Проекты</h2>

    <nav class="main-navigation">
        <ul class="main-navigation__list">
            <?php foreach ($projects as $project) : ?>
            <li class="main-navigation__list-item <?php if ($project_id == $project["id"]) { print("main-navigation__list-item--active"); } ?>">
                <a class="main-navigation__list-item-link" href="/?project_id=<?= $project["id"]; ?>"><?= htmlspecialchars($project["title"]); ?></a>
                <span class="main-navigation__list-item-count"><?= getProjectCount($tasks_list, $project["title"]); ?></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <a class="button button--transparent button--plus content__side-button" href="/project.php" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
    <h2 class="content__main-heading">Список задач</h2>

    <form class="search-form" action="index.php" method="get" autocomplete="off">
        <input class="search-form__input" type="text" name="search" value="" placeholder="Поиск по задачам">

        <input class="search-form__submit" type="submit" name="" value="Искать">
    </form>

    <div class="tasks-controls">
        <nav class="tasks-switch">
            <a href="/?tasks_switch_mode=all" class="tasks-switch__item <?php if ($tasks_switch_mode == "all") { print(" tasks-switch__item--active"); } ?>">Все задачи</a>
            <a href="/?tasks_switch_mode=today" class="tasks-switch__item <?php if ($tasks_switch_mode == "today") { print(" tasks-switch__item--active"); } ?>">Повестка дня</a>
            <a href="/?tasks_switch_mode=tomorrow" class="tasks-switch__item <?php if ($tasks_switch_mode == "tomorrow") { print(" tasks-switch__item--active"); } ?>">Завтра</a>
            <a href="/?tasks_switch_mode=expired" class="tasks-switch__item <?php if ($tasks_switch_mode == "expired") { print(" tasks-switch__item--active"); } ?>">Просроченные</a>
        </nav>

        <label class="checkbox">
            <!--добавить сюда атрибут "checked", если переменная $show_complete_tasks равна единице-->
            <input class="checkbox__input visually-hidden show_completed" type="checkbox" <?php if ($show_complete_tasks === 1) { print("checked"); } ?>>
            <span class="checkbox__text"><a href="/?show_complete_tasks=toggle" style="color:#31313a;">Показывать выполненные</a></span>
        </label>
    </div>
    
    <?php if (count($param_tasks_list) === 0) : ?>
    
        <p>Ничего не найдено по вашему запросу</p>
        
    <?php else : ?>
    
        <table class="tasks">
            <?php foreach ($param_tasks_list as $tasks_list_item) : 
                if (($tasks_list_item["status"] === 1) && ($show_complete_tasks === 0)) { continue; }
            ?>
            <tr class="tasks__item task <?php if ($tasks_list_item["status"] === 1) { print("task--completed"); } 
                if (isLessThan24($tasks_list_item["task_expiration"])) { print(" task--important"); }
            ?>">
                <td class="task__select">
                    <label class="checkbox task__checkbox">
                        <input class="checkbox__input visually-hidden task__checkbox" type="checkbox" value="1" <?php if ($tasks_list_item["status"] === 1) { print("checked"); } ?>>
                        <span class="checkbox__text"><a href="/?task_id_for_change_status=<?= $tasks_list_item["tid"]; ?>" style="color:#31313a;"><?= htmlspecialchars($tasks_list_item["task_title"]); ?></a></span>
                    </label>
                </td>
                <td class="task__file">
                    <?php if (isset($tasks_list_item["file_path"])) : ?>
                    <a class="download-link" href="<?= "/uploads/" . $tasks_list_item["file_path"]; ?>"><?= $tasks_list_item["file_path"]; ?></a>
                    <?php endif; ?>
                </td>
                <td class="task__date"><?= timestampToNormal($tasks_list_item["task_expiration"]); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    
    <?php endif; ?>
    
</main>
