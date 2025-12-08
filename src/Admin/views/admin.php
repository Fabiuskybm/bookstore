<?php
declare(strict_types=1);
?>

<section class="admin">
    <h1 class="admin__title">
        <?= e(t('admin.title')) ?>
    </h1>

    <div class="admin__divider"></div>

    <div class="admin__card">
        <img 
            src="assets/images/utils/under-construction.png" 
            alt="<?= e(t('admin.under_construction_alt')) ?>"
            class="admin__image"
        >

        <p class="admin__text">
            <?= e(t('admin.message')) ?>
        </p>
    </div>
</section>
