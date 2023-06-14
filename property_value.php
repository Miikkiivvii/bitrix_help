<?php
// Подключаем API Битрикс
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Context;
use Bitrix\Iblock\ElementTable;

try {
    // Проверяем, что модуль "Инфоблоки" установлен
    if (!Loader::includeModule('iblock')) {
        throw new LoaderException('Модуль "Инфоблоки" не установлен');
    }

    // ID инфоблока
    $iblockId = 27;
    // Значение свойства HARDWARE_TYPE
    $propertyValue = 'OLIVIA';

    // Получаем список элементов инфоблока
    $elements = ElementTable::getList(array(
        'filter' => array('IBLOCK_ID' => $iblockId),
        'select' => array('ID')
    ));

    // Обновляем значение свойства для каждого элемента
    while ($element = $elements->fetch()) {
        CIBlockElement::SetPropertyValueCode($element['ID'], 'HARDWARE_TYPE', $propertyValue);
    }

    echo 'Значение свойства успешно обновлено для всех элементов инфоблока с ID ' . $iblockId;
} catch (Exception $e) {
    echo 'Ошибка: ' . $e->getMessage();
}
?>
