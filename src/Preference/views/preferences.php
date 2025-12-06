<?php
declare(strict_types=1);

$currentTheme = pref_theme();
$currentItems = pref_items_per_page();
?>


<div class="preferences">
    <h1 class="preferences__title"><?= e(t('preferences.title')) ?></h1>

    <form class="preferences__form" action="index.php" method="POST">

        <input type="hidden" name="action" value="preferences_update">
        <input type="hidden" name="_return" value="preferences">


        <!-- Tema -->
        <div class="preferences__group">
            <label class="preferences__label"><?= e(t('preferences.theme')) ?></label>

            <select class="preferences__select" name="theme">
                <option value="light" <?= $currentTheme === 'light' ? 'selected' : '' ?>>
                    <?= e(t('preferences.theme_light')) ?>
                </option>

                <option value="dark" <?= $currentTheme === 'dark' ? 'selected' : '' ?>>
                    <?= e(t('preferences.theme_dark')) ?>
                </option>
            </select>
        </div>


        <!-- Items por página -->
        <div class="preferences__group">
            <label class="preferences__label"><?= e(t('preferences.items_per_page')) ?></label>

            <select
                class="preferences__select"
                name="items_per_page">
                
                <?php 
                    // Valores permitidos (modifícalos si quieres)
                    $options = [4, 8, 12, 16, 20];
                ?>

                <?php foreach ($options as $opt): ?>
                    <option 
                        value="<?= $opt ?>" 
                        <?= ($currentItems == $opt) ? 'selected' : '' ?>>
                        <?= $opt ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>


        <!-- Modo de vista (solo JS, localStorage) -->
        <div class="preferences__group preferences__group--view">
            <span class="preferences__label"><?= e(t('preferences.view_mode')) ?></span>

            <div class="preferences__view-toggle" data-view-toggle>
                <button
                    type="button"
                    class="preferences__view-button preferences__view-button--normal"
                    data-view-mode="normal"
                    aria-pressed="false"
                    title="<?= e(t('preferences.view_normal')) ?>"
                >
                    <img
                        src="assets/images/icons/grid-view.svg"
                        alt="<?= e(t('preferences.view_normal_alt')) ?>"
                        class="preferences__view-icon"
                    >
                </button>

                <button
                    type="button"
                    class="preferences__view-button preferences__view-button--compact"
                    data-view-mode="compact"
                    aria-pressed="false"
                    title="<?= e(t('preferences.view_compact')) ?>"
                >
                    <img
                        src="assets/images/icons/compact-view.svg"
                        alt="<?= e(t('preferences.view_compact_alt')) ?>"
                        class="preferences__view-icon"
                    >
                </button>

            </div>

            <p class="preferences__note">
                <?= e(t('preferences.compact_view_note')) ?>
            </p>
        </div>


        <button 
            class="preferences__submit" 
            type="submit">
            <?= e(t('preferences.save_button')) ?>
        </button>

    </form>
</div>
