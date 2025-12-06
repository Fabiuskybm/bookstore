<?php
declare(strict_types=1);
?>

<footer class="page__footer footer">
    <div class="footer__inner">

        <p class="footer__text">
            <?= e(t('footer.copyright')) ?>
        </p>

        <div 
            class="footer__social" 
            aria-label="<?= e(t('footer.social_label')) ?>"
        >
            <ul class="footer__social-list">

                <li class="footer__social-item">
                    <a 
                        href="https://www.instagram.com/editorial.blackrose/"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <img 
                            src="assets/images/icons/instagram.svg"
                            alt="<?= e(t('footer.instagram_alt')) ?>"
                            class="footer__social-icon"
                        >
                    </a>
                </li>

                <li class="footer__social-item">
                    <a 
                        href="https://www.tiktok.com/@editorial.blackros"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <img 
                            src="assets/images/icons/tiktok.svg"
                            alt="<?= e(t('footer.tiktok_alt')) ?>"
                            class="footer__social-icon"
                        >
                    </a>
                </li>

                <li class="footer__social-item">
                    <a 
                        href="https://x.com/editblackrose"
                        class="footer__social-link"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <img 
                            src="assets/images/icons/x.svg"
                            alt="<?= e(t('footer.x_alt')) ?>"
                            class="footer__social-icon"
                        >
                    </a>
                </li>

            </ul>
        </div>

    </div>
</footer>
