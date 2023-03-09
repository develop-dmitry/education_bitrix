<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die;

use Bitrix\Main\Context;
use Bitrix\Main\UserTable;

class YandexAuthComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        if ($this->authorize()) {
            LocalRedirect($this->arParams['REDIRECT_URI']);
        }

        $this->arResult['AUTHORIZATION_URL'] = $this->getAuthorizeUrl();

        $this->includeComponentTemplate();
    }

    public function onPrepareComponentParams($arParams)
    {
        $arParams = parent::onPrepareComponentParams($arParams);

        $server = Context::getCurrent()->getServer();

        $arParams['YANDEX_REDIRECT_URI'] = $server->getRequestScheme() . '://' . $server->getHttpHost() . $server->getRequestUri();
        $arParams['REDIRECT_URI'] = $arParams['REDIRECT_URI'] ?? '';
        $arParams['CLIENT_ID'] = $arParams['CLIENT_ID'] ?? '';
        $arParams['CLIENT_SECRET'] = $arParams['CLIENT_SECRET'] ?? '';
        $arParams['TOKEN_PROPERTY'] = $arParams['TOKEN_PROPERTY'] ?? 'UF_YANDEX_TOKEN';

        return $arParams;
    }

    protected function getAuthorizeUrl(): string
    {
        $params = [
            'client_id' => $this->arParams['CLIENT_ID'],
            'redirect_uri' => $this->arParams['YANDEX_REDIRECT_URI'],
            'response_type' => 'code',
        ];

        return  'https://oauth.yandex.ru/authorize?' . http_build_query($params);
    }

    protected function getAuthorizationToken(string $code): string
    {
        $params = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->arParams['CLIENT_ID'],
            'client_secret' => $this->arParams['CLIENT_SECRET']
        ];

        $ch = curl_init('https://oauth.yandex.ru/token');

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);

        $data = curl_exec($ch);

        curl_close($ch);

        $response = json_decode($data, true);

        return $response['access_token'] ?? '';
    }

    protected function authorize(): bool
    {
        global $USER;

        if ($USER->IsAuthorized()) {
            return true;
        }

        $code = $this->request->get('code');

        if ($code) {
            $token = $this->getAuthorizationToken($code);

            if ($token) {
                try {
                    $userId = $this->getUserIdByToken($token);
                    $USER->Authorize($userId);

                    return true;
                } catch (Exception $exception) {
                    $this->arResult['ERRORS'][] = $exception->getMessage();

                    return false;
                }
            }
        }

        return false;
    }

    /**
     * @throws Exception
     */
    protected function getUserIdByToken(string $token): int
    {
        $userParams = [
            'select' => ['ID'],
            'filter' => [
                $this->arParams['TOKEN_PROPERTY'] => $token
            ]
        ];

        $user = UserTable::getList($userParams)->fetch();

        if (!$user) {
            throw new Exception('Яндекс.ID не привязан ни к одному аккаунту');
        }

        return $user['ID'];
    }
}