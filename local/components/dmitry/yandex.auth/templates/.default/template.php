<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die; ?>

<?php if ($arResult['ERRORS']): ?>
    <ul class="auth-error">
        <?php foreach ($arResult['ERRORS'] as $error): ?>
            <li class="auth-error__item"><?=$error?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<a href="<?=$arResult['AUTHORIZATION_URL']?>">Авторизоваться через Яндекс.ID</a>