<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../Shared/user-menu.php';


$items = $currentUser ? get_user_menu_items($currentUser) : [];

?>


<?php if (!empty($items)): ?>

    <ul class="header__user-menu-list">

        <?php foreach ($items as $item): ?>

            <li class="header__user-menu-item">

                <?php if ($item['type'] === 'link'): ?>

                    <a 
                        href="index.php?view=<?= e($item['view']) ?>"
                        class="header__user-menu-link">
                        <?= e(t('user_menu.' . $item['key'])) ?>
                    </a>

                <?php elseif ($item['type'] === 'logout'): ?>

                    <form 
                        action="index.php?view=home" 
                        method="post"
                        class="header__user-menu-form">

                        <button 
                            type="submit"
                            name="action"
                            value="logout"
                            class="header__user-menu-action">
                            <?= e(t('user_menu.' . $item['key'])) ?>
                        </button>
                    </form>

                <?php endif; ?>

            </li>

        <?php endforeach; ?>

    </ul>

<?php endif; ?>
