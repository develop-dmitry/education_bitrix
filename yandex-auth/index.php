<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация через Яндекс.ID");
?>

<?php $APPLICATION->IncludeComponent(
    "dmitry:yandex.auth",
    "",
    Array(
        'CLIENT_ID' => '656313bb9c194ac18ef311a676c68131',
        'CLIENT_SECRET' => 'a60d39483bc7475c893029875d24bb77',
        'REDIRECT_URI' => '/auth',
        'TOKEN_PROPERTY' => 'UF_YANDEX_TOKEN'
    ),
    false
); ?>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>