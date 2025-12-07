<?php
declare(strict_types=1);

$currentTheme = pref_theme();
$currentItems = pref_items_per_page();
?>


<div class="preferences">
    <h1 class="preferences__title"><?= e(t('preferences.title')) ?></h1>
    <div class="preferences__divider"></div>

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
                    <svg
                        class="preferences__view-icon preferences__view-icon--grid"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M8 1C9.65685 1 11 2.34315 11 4V8C11 9.65685 9.65685 11 8 11H4C2.34315 11 1 9.65685 1 8V4C1 2.34315 2.34315 1 4 1H8ZM8 3C8.55228 3 9 3.44772 9 4V8C9 8.55228 8.55228 9 8 9H4C3.44772 9 3 8.55228 3 8V4C3 3.44772 3.44772 3 4 3H8Z"
                            fill="currentColor"
                        />
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M8 13C9.65685 13 11 14.3431 11 16V20C11 21.6569 9.65685 23 8 23H4C2.34315 23 1 21.6569 1 20V16C1 14.3431 2.34315 13 4 13H8ZM8 15C8.55228 15 9 15.4477 9 16V20C9 20.5523 8.55228 21 8 21H4C3.44772 21 3 20.5523 3 20V16C3 15.4477 3.44772 15 4 15H8Z"
                            fill="currentColor"
                        />
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M23 4C23 2.34315 21.6569 1 20 1H16C14.3431 1 13 2.34315 13 4V8C13 9.65685 14.3431 11 16 11H20C21.6569 11 23 9.65685 23 8V4ZM21 4C21 3.44772 20.5523 3 20 3H16C15.4477 3 15 3.44772 15 4V8C15 8.55228 15.4477 9 16 9H20C20.5523 9 21 8.55228 21 8V4Z"
                            fill="currentColor"
                        />
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M20 13C21.6569 13 23 14.3431 23 16V20C23 21.6569 21.6569 23 20 23H16C14.3431 23 13 21.6569 13 20V16C13 14.3431 14.3431 13 16 13H20ZM20 15C20.5523 15 21 15.4477 21 16V20C21 20.5523 20.5523 21 20 21H16C15.4477 21 15 20.5523 15 20V16C15 15.4477 15.4477 15 16 15H20Z"
                            fill="currentColor"
                        />
                    </svg>

                </button>

                <button
                    type="button"
                    class="preferences__view-button preferences__view-button--compact"
                    data-view-mode="compact"
                    aria-pressed="false"
                    title="<?= e(t('preferences.view_compact')) ?>"
                >
                    <svg
                        class="preferences__view-icon preferences__view-icon--compact"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                    >
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M9 6C9 4.34315 7.65685 3 6 3H4C2.34315 3 1 4.34315 1 6V8C1 9.65685 2.34315 11 4 11H6C7.65685 11 9 9.65685 9 8V6ZM7 6C7 5.44772 6.55228 5 6 5H4C3.44772 5 3 5.44772 3 6V8C3 8.55228 3.44772 9 4 9H6C6.55228 9 7 8.55228 7 8V6Z"
                            fill="currentColor"
                        />
                        <path
                            fill-rule="evenodd"
                            clip-rule="evenodd"
                            d="M9 16C9 14.3431 7.65685 13 6 13H4C2.34315 13 1 14.3431 1 16V18C1 19.6569 2.34315 21 4 21H6C7.65685 21 9 19.6569 9 18V16ZM7 16C7 15.4477 6.55228 15 6 15H4C3.44772 15 3 15.4477 3 16V18C3 18.5523 3.44772 19 4 19H6C6.55228 19 7 18.5523 7 18V16Z"
                            fill="currentColor"
                        />
                        <path
                            d="M11 7C11 6.44772 11.4477 6 12 6H22C22.5523 6 23 6.44772 23 7C23 7.55228 22.5523 8 22 8H12C11.4477 8 11 7.55228 11 7Z"
                            fill="currentColor"
                        />
                        <path
                            d="M11 17C11 16.4477 11.4477 16 12 16H22C22.5523 16 23 16.4477 23 17C23 17.5523 22.5523 18 22 18H12C11.4477 18 11 17.5523 11 17Z"
                            fill="currentColor"
                        />
                    </svg>

                </button>

            </div>

        </div>
        
        <p class="preferences__note">
            <?= e(t('preferences.compact_view_note')) ?>
        </p>

        <button 
            class="preferences__submit" 
            type="submit">
            <?= e(t('preferences.save_button')) ?>
        </button>

    </form>
</div>
