<h1>To do Lists</h1>

<ul>
    <?php foreach($todoLists as $todoList): ?>
    <li>
        <?= $todoList->title ?>
        <?= $todoList->description ?>
    </li>
    <?php endforeach; ?>
</ul>