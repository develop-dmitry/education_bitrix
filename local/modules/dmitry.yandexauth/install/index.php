<?php

use Bitrix\Main\ModuleManager;

class dmitry_yandexauth extends CModule
{
    private string $userPropertyCode = 'UF_YANDEX_TOKEN';

    public function __construct()
    {
        $arModuleVersion = array();

        include_once(__DIR__."/version.php");

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_ID = str_replace('_', '.', get_class($this));
        $this->MODULE_NAME = 'Авторизация Яндекс.ID';
        $this->MODULE_DESCRIPTION = 'Позволяет реализовать авторизацию на сайте через Яндекс.ID';
        $this->PARTNER_NAME = 'dmitry';
    }

    public function DoInstall()
    {
        ModuleManager::registerModule($this->MODULE_ID);

        $this->InstallFiles();
        $this->InstallDB();
    }

    public function InstallFiles()
    {
        CopyDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);
    }

    public function InstallDB()
    {
        $userTypeEntity = new CUserTypeEntity();

        $userTypeEntity->Add([
            'ENTITY_ID' => 'USER',
            'FIELD_NAME' => $this->userPropertyCode,
            'USER_TYPE_ID' => 'string',
            'EDIT_FORM_LABEL'   => array(
                'ru' => 'Токен Яндекс.ID',
                'en' => 'Yandex.ID token',
            ),
        ]);
    }

    public function DoUninstall()
    {
        $this->UnInstallFiles();
        $this->UnInstallDB();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    public function UnInstallFiles()
    {
        DeleteDirFiles(__DIR__ . '/components', $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components');
    }

    public function UnInstallDB()
    {
        $propertyId = $this->getUserPropertyId();

        if ($propertyId) {
            $userTypeEntity = new CUserTypeEntity();

            $userTypeEntity->Delete($propertyId);
        }
    }

    private function getUserPropertyId(): ?int
    {
        $response = CUserTypeEntity::GetList([], ['FIELD_NAME' => $this->userPropertyCode])->Fetch();

        if ($response) {
            return (int) $response['ID'];
        }

        return null;
    }
}