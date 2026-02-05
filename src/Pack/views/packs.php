<?php
declare(strict_types=1);

$packs = $data['packs'] ?? [];
$totals = $data['totals'] ?? ['grand_total' => 0, 'by_category' => []];
$books = $data['books'] ?? [];
$categories = $data['categories'] ?? [];
$booksByCategory = $data['booksByCategory'] ?? [];
$errors = $data['errors'] ?? [];
$form = $data['form'] ?? [];
?>

<section class="packs">
    <h1 class="packs__title"><?= e(t('packs.title')) ?></h1>

    <form method="post" class="packs__form">
        <input type="hidden" name="action" value="pack_add">

        <div class="packs__field">
            <label for="pack-name"><?= e(t('packs.form.pack_name_label')) ?></label>
            <input
                id="pack-name"
                type="text"
                name="name"
                class="packs__input"
                value="<?= e($form['name'] ?? '') ?>">

            <?php if (isset($errors['name'])): ?>
                <p class="packs__error"><?= e(t($errors['name'])) ?></p>
            <?php endif; ?>
        </div>

        <div class="packs__field">
            <label for="pack-category"><?= e(t('packs.form.pack_category_label')) ?></label>

            <div class="packs__select">

                <select
                    id="pack-category"
                    name="category"
                    class="packs__select-input"
                    <?= empty($categories) ? 'disabled' : '' ?>>
                    <option value=""><?= e(t('packs.form.pack_category_placeholder')) ?></option>

                    <?php foreach ($categories as $category): ?>
                        <option
                            value="<?= e($category) ?>"
                            <?= ($form['category'] ?? '') === $category ? 'selected' : '' ?>>
                            <?= e(t('categories.' . $category, $category)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <span class="packs__select-icon" aria-hidden="true">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>                
            </div>

            <?php if (empty($categories)): ?>
                <p class="packs__hint"><?= e(t('packs.form.pack_category_empty')) ?></p>
            <?php endif; ?>
            <?php if (isset($errors['category'])): ?>
                <p class="packs__error"><?= e(t($errors['category'])) ?></p>
            <?php endif; ?>
        </div>

        <div class="packs__field packs__field--action">
            <button type="submit" class="packs__submit" <?= empty($categories) ? 'disabled' : '' ?>>
                <?= e(t('packs.form.pack_submit')) ?>
            </button>
        </div>
    </form>

    <div class="packs__list">
        <h2><?= e(t('packs.list.title')) ?></h2>

        <?php if (empty($packs)): ?>
            <p><?= e(t('packs.list.empty')) ?></p>
        <?php else: ?>
            <?php foreach ($packs as $index => $pack): ?>
                <?php
                    $packCategory = (string) ($pack['category'] ?? '');
                    $packItems = $pack['items'] ?? [];
                    $availableBooks = $booksByCategory[$packCategory] ?? [];
                ?>

                <article class="packs__card">
                    <header class="packs__card-header">
                        <div>
                            <h3><?= e($pack['name'] ?? '') ?></h3>
                            <p class="packs__meta">
                                <?= e(t('categories.' . $packCategory, $packCategory)) ?>
                            </p>
                        </div>

                        <form method="post">
                            <input type="hidden" name="index" value="<?= e($index) ?>">
                            <button type="submit" name="action" value="pack_remove" class="packs__remove">
                                <?= e(t('packs.list.remove_pack')) ?>
                            </button>
                        </form>
                    </header>

                    <div class="packs__card-body">
                        <form method="post" class="packs__add-item">
                            <input type="hidden" name="action" value="pack_item_add">
                            <input type="hidden" name="pack_index" value="<?= e($index) ?>">

                            <div class="packs__field">
                                <label for="pack-book-<?= e($index) ?>"><?= e(t('packs.items.book_label')) ?></label>
                                <div class="packs__select">
                                    <select
                                        id="pack-book-<?= e($index) ?>"
                                        name="book_id"
                                        class="packs__select-input"
                                        <?= empty($availableBooks) ? 'disabled' : '' ?>>
                                        <option value=""><?= e(t('packs.items.book_placeholder')) ?></option>

                                        <?php foreach ($availableBooks as $book): ?>
                                            <?php
                                                $selected = (string) ($form['book_id'] ?? '') === (string) $book->getId()
                                                    && (string) ($form['pack_index'] ?? '') === (string) $index;
                                            ?>
                                            <option value="<?= e($book->getId()) ?>" <?= $selected ? 'selected' : '' ?>>
                                                <?= e($book->getName()) ?>
                                                (<?= number_format($book->getPrice(), 2, ',', '.') ?> €)
                                                - <?= e(t('packs.items.stock_label')) ?>: <?= e($book->getStock()) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <span class="packs__select-icon" aria-hidden="true">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </span>
                                </div>
                                
                                <?php if (empty($availableBooks)): ?>
                                    <p class="packs__hint"><?= e(t('packs.items.book_empty')) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="packs__field">
                                <label for="pack-qty-<?= e($index) ?>"><?= e(t('packs.items.quantity_label')) ?></label>
                                <input
                                    id="pack-qty-<?= e($index) ?>"
                                    type="number"
                                    name="quantity"
                                    min="1"
                                    class="packs__input"
                                    value="<?= e($form['quantity'] ?? 1) ?>">
                            </div>

                            <div class="packs__field packs__field--action">
                                <button type="submit" class="packs__add-btn" <?= empty($availableBooks) ? 'disabled' : '' ?>>
                                    <?= e(t('packs.items.add_button')) ?>
                                </button>
                            </div>
                        </form>

                        <?php if (!empty($errors) && (string) ($form['pack_index'] ?? '') === (string) $index): ?>
                            <p class="packs__error">
                                <?= e(t($errors['book_id'] ?? $errors['quantity'] ?? $errors['pack_index'] ?? '')) ?>
                            </p>
                        <?php endif; ?>

                        <?php if (empty($packItems)): ?>
                            <p class="packs__hint"><?= e(t('packs.items.empty')) ?></p>
                        <?php else: ?>
                            <table class="packs__table">
                                <thead>
                                    <tr>
                                        <th><?= e(t('packs.items.table_book')) ?></th>
                                        <th><?= e(t('packs.items.table_quantity')) ?></th>
                                        <th><?= e(t('packs.items.table_price')) ?></th>
                                        <th><?= e(t('packs.items.table_total')) ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($packItems as $item): ?>
                                        <tr>
                                            <td><?= e($item['name'] ?? '') ?></td>
                                            <td><?= e($item['quantity'] ?? '') ?></td>
                                            <td><?= number_format((float) ($item['price'] ?? 0), 2, ',', '.') ?> €</td>
                                            <td><?= number_format((float) ($item['total'] ?? 0), 2, ',', '.') ?> €</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>

                    <footer class="packs__card-footer">
                        <strong><?= e(t('packs.items.pack_total')) ?></strong>
                        <?= number_format((float) ($pack['total'] ?? 0), 2, ',', '.') ?> €
                    </footer>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="packs__totals">
        <h2><?= e(t('packs.totals.title')) ?></h2>
        <p><strong><?= e(t('packs.totals.grand_total')) ?></strong> <?= number_format((float) ($totals['grand_total'] ?? 0), 2, ',', '.') ?> €</p>

        <h3><?= e(t('packs.totals.by_category')) ?></h3>
        <ul>
            <?php foreach (($totals['by_category'] ?? []) as $category => $total): ?>
                <li>
                    <?= e(t('categories.' . $category, $category)) ?>:
                    <?= number_format((float) $total, 2, ',', '.') ?> €
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div class="packs__actions packs__actions--bottom">
        <form method="post">
            <button type="submit" name="action" value="pack_clear" class="packs__btn packs__btn--clear">
                <?= e(t('packs.actions.clear')) ?>
            </button>
        </form>
    </div>
</section>
