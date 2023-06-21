<?php

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');

CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');

$mainIblockId = 27;
$offerIblockId = 28;

$arSelect = array(
    "ID",
    "NAME",
    "PROPERTY_HARDWARE_TYPE",
    "PROPERTY_COLORS",
    "PROPERTY_LENGTHS",
    "PROPERTY_DISTANCES",
    "CATALOG_PRICE_1",
    "PREVIEW_PICTURE",
    "DETAIL_PICTURE",
    "CODE",
);
$arFilter = array(
    "IBLOCK_ID" => $mainIblockId,
    "ACTIVE" => "Y",
    "!PROPERTY_HARDWARE_TYPE" => false,
    "!PROPERTY_COLORS" => false,
    "!PROPERTY_LENGTHS" => false,
    "!PROPERTY_DISTANCES" => false
);
$res = CIBlockElement::GetList(array("PROPERTY_HARDWARE_TYPE" => "ASC"), $arFilter, false, false, $arSelect);

$addedOffers = array(); // Массив для отслеживания добавленных предложений

// Загрузка информации о добавленных предложениях из файла
$filePath = $_SERVER['DOCUMENT_ROOT'] . '/added_offers.txt';
if (file_exists($filePath)) {
    $addedOffers = unserialize(file_get_contents($filePath));
}

$uniqueHardwareTypes = array();

while ($ob = $res->GetNextElement()) {
    $arFields = $ob->GetFields();

    if (!isset($arFields["PROPERTY_HARDWARE_TYPE_VALUE"])) {
        continue;
    }

    $hardwareType = $arFields["PROPERTY_HARDWARE_TYPE_VALUE"];

    if (!in_array($hardwareType, $uniqueHardwareTypes)) {
        $uniqueHardwareTypes[] = $hardwareType;
    }
}

foreach ($uniqueHardwareTypes as $hardwareType) {
    $arSelect = array(
        "ID",
        "NAME",
        "PROPERTY_HARDWARE_TYPE",
        "PROPERTY_COLORS",
        "PROPERTY_LENGTHS",
        "PROPERTY_DISTANCES",
        "CATALOG_PRICE_1",
        "PREVIEW_PICTURE",
        "CODE",
        "DETAIL_PICTURE",
    );
    $arFilter = array(
        "IBLOCK_ID" => $mainIblockId,
        "ACTIVE" => "Y",
        "PROPERTY_HARDWARE_TYPE" => $hardwareType,
        "!PROPERTY_COLORS" => false,
        "!PROPERTY_LENGTHS" => false,
        "!PROPERTY_DISTANCES" => false
    );
    $res = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

    $existingProduct = CIBlockElement::GetList(
        array(),
        array("IBLOCK_ID" => $mainIblockId, "NAME" => $hardwareType,),
        false,
        false,
        array("ID")
    )->Fetch();

    if ($existingProduct) {
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $skuElement = new CIBlockElement();

            $productPrice = $arFields["CATALOG_PRICE_1"];
            $productCurrency = "RUB";

            $skuProps = array(
                "CVET" => $arFields["PROPERTY_COLORS_VALUE"],
                "DLINA" => $arFields["PROPERTY_LENGTHS_VALUE"],
                "DISTANCE" => $arFields["PROPERTY_DISTANCES_VALUE"],
            );

            $skuFields = array(
                "IBLOCK_ID" => $offerIblockId,
                "NAME" => $arFields["NAME"],
                "CODE" => $arFields["CODE"],
                "PROPERTY_VALUES" => $skuProps,

                "PREVIEW_PICTURE" => CFile::MakeFileArray($arFields["PREVIEW_PICTURE"]),
                "DETAIL_PICTURE" => CFile::MakeFileArray($arFields["DETAIL_PICTURE"]),
            );

            $offerKey = $hardwareType . '_' . $arFields["NAME"]; // Ключ предложения

            if (isset($addedOffers[$offerKey])) {
                continue;
            }

            $skuId = $skuElement->Add($skuFields);

            if ($skuId) {
                $arFieldsPrice = array(
                    "PRODUCT_ID" => $skuId,
                    "CATALOG_GROUP_ID" => 1,
                    "PRICE" => $productPrice,
                    "CURRENCY" => $productCurrency,
                );

                $dbPrice = CPrice::GetList(
                    array(),
                    array(
                        "PRODUCT_ID" => $skuId,
                        "CATALOG_GROUP_ID" => 1
                    )
                );

                if ($arPrice = $dbPrice->Fetch()) {
                    CPrice::Update($arPrice["ID"], $arFieldsPrice);
                } else {
                    CPrice::Add($arFieldsPrice);
                }

                CIBlockElement::SetPropertyValues($skuId, $offerIblockId, $existingProduct["ID"], "CML2_LINK");

                // Добавляем предложение в массив добавленных предложений
                $addedOffers[$offerKey] = true;
            }
        }
    } else {
        $offerElement = new CIBlockElement();
        $offerProps = array("HARDWARE_TYPE" => $hardwareType);
        $offerFields = array(
            "IBLOCK_ID" => $mainIblockId,
            "NAME" => $hardwareType,
            "PROPERTY_VALUES" => $offerProps,
            "CODE" => $hardwareType,
            "IBLOCK_SECTION_ID" => 866, // Добавление к разделу 
        );

        $newProductId = $offerElement->Add($offerFields);

        if ($newProductId) {
            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $skuElement = new CIBlockElement();

                $productPrice = $arFields["CATALOG_PRICE_1"];
                $productCurrency = "RUB";

                $skuProps = array(
                    "CVET" => $arFields["PROPERTY_COLORS_VALUE"],
                    "DLINA" => $arFields["PROPERTY_LENGTHS_VALUE"],
                    "DISTANCE" => $arFields["PROPERTY_DISTANCES_VALUE"],
                );

                $skuFields = array(
                    "IBLOCK_ID" => $offerIblockId,
                    "NAME" => $arFields["NAME"],
                    "CODE" => $arFields["CODE"],
                    "PROPERTY_VALUES" => $skuProps,
                    
                    "PREVIEW_PICTURE" => CFile::MakeFileArray($arFields["PREVIEW_PICTURE"]),
                    "DETAIL_PICTURE" => CFile::MakeFileArray($arFields["DETAIL_PICTURE"]),
                );

                $offerKey = $hardwareType . '_' . $arFields["NAME"]; // Ключ предложения

                if (isset($addedOffers[$offerKey])) {
                    continue;
                }

                $skuId = $skuElement->Add($skuFields);

                if ($skuId) {
                    $arFieldsPrice = array(
                        "PRODUCT_ID" => $skuId,
                        "CATALOG_GROUP_ID" => 1,
                        "PRICE" => $productPrice,
                        "CURRENCY" => $productCurrency,
                    );

                    $dbPrice = CPrice::GetList(
                        array(),
                        array(
                            "PRODUCT_ID" => $skuId,
                            "CATALOG_GROUP_ID" => 1
                        )
                    );

                    if ($arPrice = $dbPrice->Fetch()) {
                        CPrice::Update($arPrice["ID"], $arFieldsPrice);
                    } else {
                        CPrice::Add($arFieldsPrice);
                    }

                    CIBlockElement::SetPropertyValues($skuId, $offerIblockId, $newProductId, "CML2_LINK");

                    // Добавляем предложение в массив добавленных предложений
                    $addedOffers[$offerKey] = true;
                }
            }
        }
    }
}

// Сохранение информации о добавленных предложениях в файл
file_put_contents($filePath, serialize($addedOffers));

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
?>
