<?php
declare(strict_types=1);

$currentLang  = pref_language();
$currentView  = $_GET['view'] ?? 'home';
$currentQuery = $_SERVER['QUERY_STRING'] ?? '';

if ($currentQuery === '') {
    $currentQuery = 'view=' . $currentView;
}
?>

<ul class="header__lang-menu-list">

    <li class="header__lang-menu-item">
        <form 
            action="index.php?view=<?= e($currentView) ?>"
            method="post"
            class="header__lang-menu-form"
        >
            <input type="hidden" name="action" value="set_language">
            <input type="hidden" name="_return" value="<?= e($currentView) ?>">
            <input type="hidden" name="_return_query" value="<?= e($currentQuery) ?>">

            <button
                type="submit"
                name="language"
                value="es"
                class="header__lang-menu-button"
                <?= $currentLang === 'es' ? 'aria-current="true"' : '' ?>
            >
                <?= e(t('header.language_es_label')) ?>
            </button>
        </form>
    </li>

    <li class="header__lang-menu-item">
        <form 
            action="index.php?view=<?= e($currentView) ?>"
            method="post"
            class="header__lang-menu-form"
        >
            <input type="hidden" name="action" value="set_language">
            <input type="hidden" name="_return" value="<?= e($currentView) ?>">
            <input type="hidden" name="_return_query" value="<?= e($currentQuery) ?>">

            <button
                type="submit"
                name="language"
                value="en"
                class="header__lang-menu-button"
                <?= $currentLang === 'en' ? 'aria-current="true"' : '' ?>
            >
                <?= e(t('header.language_en_label')) ?>
            </button>
        </form>
    </li>

</ul>
