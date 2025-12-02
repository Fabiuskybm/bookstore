<?php
declare(strict_types=1);
?>

<section class="home">
    <h1 class="home__title">Bienvenido a Bookstore</h1>
    <p class="home__subtitle">Aquí aparecerán los libros y el contenido principal.</p>

    <?php if (!empty($data['books'])): ?>

        <ul>
            <?php foreach($data['books'] as $book): ?>
                <li><?= e($book->getTitle()) ?> - <?= e($book->getAuthor()) ?></li>
            <?php endforeach; ?>
        </ul>

    <?php else: ?>
        <p>No hay libros disponibles</p>
    <?php endif; ?>

</section>
