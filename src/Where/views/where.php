<?php
declare(strict_types=1);
?>

<section class="where">
  <div class="where__header">
    <h1 class="where__title"><?= t('where.title') ?></h1>
    <p class="where__text"><?= t('where.intro') ?></p>
  </div>

  <div class="where__content">
    <div class="where__panel">
      <h2 class="where__store-title"><?= t('where.store_title') ?></h2>
      <p class="where__store-note"><?= t('where.store_note') ?></p>

      <div class="where__actions">
        <button class="where__btn" type="button" id="where-locate-btn">
          <?= t('where.btn_locate') ?>
        </button>

        <a
          class="where__link"
          id="where-directions-link"
          href="#"
          data-destination="<?= e(STORE_LAT . ',' . STORE_LNG) ?>"
          target="_blank"
          rel="noopener"
        >
          <?= t('where.btn_directions') ?>
        </a>

      </div>

      <p
        class="where__status"
        id="where-status"
        aria-live="polite"
        data-ready="<?= e(t('where.status_ready')) ?>"
        data-locating="<?= e(t('where.status_locating')) ?>"
        data-located="<?= e(t('where.status_located')) ?>"
        data-denied="<?= e(t('where.status_denied')) ?>"
        data-unavailable="<?= e(t('where.status_unavailable')) ?>"
        data-error="<?= e(t('where.status_error')) ?>"
      >
        <?= t('where.status_ready') ?>
      </p>

    </div>

    <div
      class="where__map"
      id="where-map"
      data-popup="<?= e(t('where.store_title')) ?>"
      data-store-lat="<?= e((string) STORE_LAT) ?>"
      data-store-lng="<?= e((string) STORE_LNG) ?>"
      data-store-zoom="<?= e((string) STORE_ZOOM) ?>"
      aria-label="<?= e(t('where.title')) ?>">
    </div>


  </div>
</section>
