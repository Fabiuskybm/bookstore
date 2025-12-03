<?php
declare(strict_types=1);
?>

<section class="home">
    <h1 class="home__title">Bienvenido a Bookstore</h1>
    <p class="home__subtitle">Aquí aparecerán los libros y el contenido principal.</p>

    <?php if (!empty($data['books'])): ?>

        <div class="home__grid">
            <?php foreach($data['books'] as $book): ?>

                <?php require __DIR__ . '/../../Book/views/partials/book-card.php'; ?>
                
            <?php endforeach; ?>
        </div>

    <?php else: ?>
        <p>No hay libros disponibles</p>
    <?php endif; ?>

</section>
