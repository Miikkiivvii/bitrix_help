<?php
use Bitrix\Main\Loader;
use Bitrix\Catalog\ProductTable;
use Bitrix\Catalog\Model\Product;
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule('iblock');
CModule::IncludeModule('catalog');
CModule::IncludeModule('highloadblock');
// Запись свойств в список
$arFilter = array(
    "IBLOCK_ID" => 27,
    "SECTION_ID" => 866,
);

$arSelect = array(
    "ID",
    "NAME",
    "PROPERTY_DISTANCE"
);

$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

$arElements = array();
while ($arElement = $rsElements->Fetch()) {
    $arElements[] = array(
        "NAME" => $arElement["NAME"],
        "ID" => $arElement["ID"],
        "DISTANCE" => $arElement["PROPERTY_DISTANCE_VALUE"]
    );
}

use Bitrix\Highloadblock\HighloadBlockTable;

$hlblockId = 11; 

$hlblock = HighloadBlockTable::getById($hlblockId)->fetch();

$entity = HighloadBlockTable::compileEntity($hlblock);

$entityDataClass = $entity->getDataClass();

$rsItems = $entityDataClass::getList(array(
    'select' => array('*'),
));

$arItems = array();
while ($arItem = $rsItems->fetch()) {
    $arItems[] = array(
        "ID" => $arItem["ID"],
        "UF_NAME" => $arItem["UF_NAME"],
        "UF_XML_ID" => $arItem["UF_XML_ID"]
    );
}


$iblockId = 27;

foreach ($arElements as $arElement) {
    foreach ($arItems as $arItem) {
        if ($arElement["DISTANCE"] === $arItem["UF_NAME"]) {
            CIBlockElement::SetPropertyValuesEx(
                $arElement["ID"],
                $iblockId,
                array("DISTANCES" => $arItem["UF_XML_ID"])
            );
            break;
        }
    }
}


$arFilter = array(
    "IBLOCK_ID" => 27,
    "SECTION_ID" => 866,
);

$arSelect = array(
    "ID",
    "NAME",
    "PROPERTY_COLOR_PRODUCT"
);

$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

$arElements = array();
while ($arElement = $rsElements->Fetch()) {
    $arElements[] = array(
        "NAME" => $arElement["NAME"],
        "ID" => $arElement["ID"],
        "COLOR_PRODUCT" => $arElement["PROPERTY_COLOR_PRODUCT_VALUE"]
    );
}


$hlblockId = 9; 

$hlblock = HighloadBlockTable::getById($hlblockId)->fetch();

$entity = HighloadBlockTable::compileEntity($hlblock);

$entityDataClass = $entity->getDataClass();

$rsItems = $entityDataClass::getList(array(
    'select' => array('*'),
));

$arItems = array();
while ($arItem = $rsItems->fetch()) {
    $arItems[] = array(
        "ID" => $arItem["ID"],
        "UF_NAME" => $arItem["UF_NAME"],
        "UF_XML_ID" => $arItem["UF_XML_ID"]
    );
}


$iblockId = 27;

foreach ($arElements as $arElement) {
    foreach ($arItems as $arItem) {
        if ($arElement["COLOR_PRODUCT"] === $arItem["UF_NAME"]) {
            CIBlockElement::SetPropertyValuesEx(
                $arElement["ID"],
                $iblockId,
                array("COLORS" => $arItem["UF_XML_ID"])
            );
            break;
        }
    }
}

$arFilter = array(
    "IBLOCK_ID" => 27,
    "SECTION_ID" => 866,
);

$arSelect = array(
    "ID",
    "NAME",
    "PROPERTY_LENGTH"
);

$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

$arElements = array();
while ($arElement = $rsElements->Fetch()) {
    $arElements[] = array(
        "NAME" => $arElement["NAME"],
        "ID" => $arElement["ID"],
        "LENGTH" => $arElement["PROPERTY_LENGTH_VALUE"]
    );
}


$hlblockId = 10; 

$hlblock = HighloadBlockTable::getById($hlblockId)->fetch();

$entity = HighloadBlockTable::compileEntity($hlblock);

$entityDataClass = $entity->getDataClass();

$rsItems = $entityDataClass::getList(array(
    'select' => array('*'),
));

$arItems = array();
while ($arItem = $rsItems->fetch()) {
    $arItems[] = array(
        "ID" => $arItem["ID"],
        "UF_NAME" => $arItem["UF_NAME"],
        "UF_XML_ID" => $arItem["UF_XML_ID"]
    );
}


$iblockId = 27;

foreach ($arElements as $arElement) {
    foreach ($arItems as $arItem) {
        if ($arElement["LENGTH"] === $arItem["UF_NAME"]) {
            CIBlockElement::SetPropertyValuesEx(
                $arElement["ID"],
                $iblockId,
                array("LENGTHS" => $arItem["UF_XML_ID"])
            );
            break;
        }
    }
}
// Конец записи свойств в список


$mainIblockId = 27;
$offerIblockId = 28;

$arSelect = array(
    "ID",
    "NAME",
    "PROPERTY_HARDWARE_TYPE",
);
$arFilter = array(
    "IBLOCK_ID" => $mainIblockId,
    "ACTIVE" => "Y",
    "IBLOCK_SECTION_ID" => 866,
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
        "PROPERTY_MATERIAL",
        "PROPERTY_QUANTITY",
        "PROPERTY_HANDLE_TYPE",
        "ID_PRODUCT_MAIN_BLOCK",
    );
    $arFilter = array(
        "IBLOCK_ID" => $mainIblockId,
        "IBLOCK_SECTION_ID" => 866,
        "PROPERTY_HARDWARE_TYPE" => $hardwareType,
        "!PROPERTY_COLORS" => false,
        "!PROPERTY_LENGTHS" => false,
        "!PROPERTY_DISTANCES" => false,
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
            // Заполнение свойств торговых предложений
            $morePhotoValues = array();
            $morePhotoRes = CIBlockElement::GetProperty(
                $mainIblockId,
                $arFields["ID"],
                array(),
                array('CODE' => 'MORE_PHOTO')
            );
            while ($morePhoto = $morePhotoRes->Fetch()) {
                $morePhotoValues[] = CFile::MakeFileArray($morePhoto['VALUE']);
            }
            $skuProps = array(
                "ID_PRODUCT_MAIN_BLOCK" => $arFields["ID"],
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
            // Добавление характеристик к ТП


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

                // Отключение товаров 
                $mainIblockId = 27;
                $offerIblockId = 28;


                $offerNames = [];
                $offerRes = CIBlockElement::GetList([], ['IBLOCK_ID' => $offerIblockId], false, false, ['ID', 'NAME']);
                while ($offer = $offerRes->Fetch()) {
                    $offerNames[] = $offer['NAME'];
                }


                $mainRes = CIBlockElement::GetList([], ['IBLOCK_ID' => $mainIblockId], false, false, ['ID', 'NAME']);
                while ($main = $mainRes->Fetch()) {
                    $mainName = $main['NAME'];

                    
                    if (in_array($mainName, $offerNames)) {
                        $mainElement = new CIBlockElement();
                        $mainElement->Update($main['ID'], ['ACTIVE' => 'N']);
                    }
                }
                // Добавляем предложение в массив добавленных предложений
                $addedOffers[$offerKey] = true;


            }
        }
    } else {
        // Создание товаров
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
                // Свойства торговых предложений, если товар создается
                $skuProps = array(
                    "ID_PRODUCT_MAIN_BLOCK" => $arFields["ID"],
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
                // Добавление характеристик к ТП
                
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

                    // Отключение товаров 
                    $mainIblockId = 27;
                    $offerIblockId = 28;


                    $offerNames = [];
                    $offerRes = CIBlockElement::GetList([], ['IBLOCK_ID' => $offerIblockId], false, false, ['ID', 'NAME']);
                    while ($offer = $offerRes->Fetch()) {
                        $offerNames[] = $offer['NAME'];
                    }


                    $mainRes = CIBlockElement::GetList([], ['IBLOCK_ID' => $mainIblockId], false, false, ['ID', 'NAME']);
                    while ($main = $mainRes->Fetch()) {
                        $mainName = $main['NAME'];

                        
                        if (in_array($mainName, $offerNames)) {
                            $mainElement = new CIBlockElement();
                            $mainElement->Update($main['ID'], ['ACTIVE' => 'N']);
                        }
                    }

                    // Добавляем предложение в массив добавленных предложений
                    $addedOffers[$offerKey] = true;
                }
            }

        }
    }
}

// Сохранение информации о добавленных предложениях в файл
file_put_contents($filePath, serialize($addedOffers));

// Создание сущности товара

$iblockID = 28;


$arSelect = array("ID", "PROPERTY_ID_PRODUCT_MAIN_BLOCK");
$arFilter = array("IBLOCK_ID" => $iblockID);
$rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);

while ($arElement = $rsElements->Fetch()) {
    $elementID = $arElement["ID"];
    $propertyID = $arElement["PROPERTY_ID_PRODUCT_MAIN_BLOCK_VALUE"];
    $quantity = ProductTable::getById($propertyID)->fetch();

    if($quantity){
        $quantityValue = $quantity['QUANTITY'];
    }
    $productFields = array(
        "ID" => $elementID,
        "QUANTITY" => $quantityValue,
    );

    CCatalogProduct::Add($productFields);
}

//Добавление свойств

$iblockId28 = 28;
$iblockId27 = 27;

$sku = CIBlockElement::GetList(
    array(),
    array(
        "IBLOCK_ID" => $iblockId28, 
        "!PROPERTY_ID_PRODUCT_MAIN_BLOCK" => false
    ),
    false,
    false,
    array(
        "ID",
        "IBLOCK_ID", 
        "PROPERTY_ID_PRODUCT_MAIN_BLOCK",
    )
);

$propsSKU = array();
while ($obSku = $sku->GetNextElement()) {
    $arFields = $obSku->GetFields();
    $elementsMain = CIBlockElement::GetList(
        array(),
        array(
            "IBLOCK_ID" => $iblockId27, 
            "ID" => $arFields["PROPERTY_ID_PRODUCT_MAIN_BLOCK_VALUE"],
        ),
        false,
        false,
        array(
            "ID",
            "NAME",
            "PROPERTY_CML2_ARTICLE",
            "PROPERTY_MATERIAL",
            "PROPERTY_HANDLE_TYPE",
            "PROPERTY_DESIGN",
            "PROPERTY_PACKAGE",
            "PROPERTY_WEIGHT_PACK",
            "PROPERTY_PACK_VOLUM",
            "PROPERTY_COLOR_PRODUCT",
            "PROPERTY_MORE_PHOTO",
            "PROPERTY_LENGTHS",
            "PROPERTY_COLORS",
            "PROPERTY_DISTANCES",
            "PROPERTY_QUANTITY",
        )
    );
    while($obMain = $elementsMain->GetNextElement()){
        $arFieldsMain = $obMain->GetFields();
        $morePhotoValues = array();
        $morePhotoRes = CIBlockElement::GetProperty(
            $iblockId27,
            $arFieldsMain["ID"],
            array(),
            array('CODE' => 'MORE_PHOTO')
        );
        while ($morePhoto = $morePhotoRes->Fetch()) {
            $morePhotoValues[] = CFile::MakeFileArray($morePhoto['VALUE']);
        }

        $propsSKU[] = array(
            "ID" => $arFields["ID"],
            "CML2_ARTICLE" => $arFieldsMain["PROPERTY_CML2_ARTICLE_VALUE"],
            "MATERIAL" => $arFieldsMain["PROPERTY_MATERIAL_VALUE"],
            "HANDLE_TYPE" => $arFieldsMain["PROPERTY_HANDLE_TYPE_VALUE"],
            "DESIGN" => $arFieldsMain["PROPERTY_DESIGN_VALUE"],
            "PACKAGE" => $arFieldsMain["PROPERTY_PACKAGE_VALUE"],
            "WEIGHT_PACK" => $arFieldsMain["PROPERTY_WEIGHT_PACK_VALUE"],
            "PACK_VOLUM" => $arFieldsMain["PROPERTY_PACK_VOLUM_VALUE"],
            "COLOR_PRODUCT" => $arFieldsMain["PROPERTY_COLOR_PRODUCT_VALUE"],
            "MORE_PHOTO" => $morePhotoValues,
            "DLINA" => $arFieldsMain["PROPERTY_LENGTHS_VALUE"],
            "CVET" => $arFieldsMain["PROPERTY_COLORS_VALUE"],
            "DISTANCE" => $arFieldsMain["PROPERTY_DISTANCES_VALUE"],
            "QUANTITY" => $arFieldsMain["PROPERTY_QUANTITY_VALUE"],
        );
    }
}

foreach ($propsSKU as $props) {
    CIBlockElement::SetPropertyValuesEx($props["ID"], $iblockId28, $props);
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
