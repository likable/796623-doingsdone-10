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
    <h2 class="content__main-heading"><?= $error_text; ?></h2>
</main>
