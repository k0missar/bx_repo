<?php
/**
 * @var CMain $APPLICATION
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php"); ?>

<?php $APPLICATION->IncludeComponent(
	"bitrix:catalog",
	"jccatalog", // Оригинал bootstrap_v4 - это его переименованный шаблон
	array(
		// ОБЯЗАТЛЕЬНЫЕ
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "2",
		// ЧПУ
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/catalog/",
		"SEF_URL_TEMPLATES" => array(
			"sections" => "",
			"section" => "#SECTION_CODE#/",
			"element" => "#SECTION_CODE#/#ELEMENT_CODE#/",
			"compare" => "compare/",
		),
		// Кеширование
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "36000000",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		// Базовая цена
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "Y",
		// Как передаются параметры в URL - оставляем как есть
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		// Сортировка и пагинация
		"PAGE_ELEMENT_COUNT" => "15",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"OFFERS_SORT_FIELD" => "sort",
		"OFFERS_SORT_ORDER" => "desc",
		"OFFERS_SORT_FIELD2" => "id",
		"OFFERS_SORT_ORDER2" => "desc",
		// Свойства для выбора торгового предложения и для корзины
		"OFFER_TREE_PROPS" => array(
			0 => "SIZES_SHOES",
			1 => "SIZES_CLOTHES",
			2 => "COLOR_REF",
		),
		"OFFERS_CART_PROPERTIES" => array(
			0 => "SIZES_SHOES",
			1 => "SIZES_CLOTHES",
			2 => "COLOR_REF",
		),

		// ПО ЗАДАЧЕ
		"BASKET_URL" => "/personal/cart/",
		"USE_PRODUCT_QUANTITY" => "Y",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"USE_FILTER" => "Y",
		"FILTER_NAME" => "",
		//seo
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"ADD_SECTION_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"DETAIL_META_KEYWORDS" => "KEYWORDS",
		"DETAIL_META_DESCRIPTION" => "META_DESCRIPTION",
		"DETAIL_BROWSER_TITLE" => "TITLE",
		// Какие свойства выводить
		"LIST_PROPERTY_CODE" => array(
			0 => "NEWPRODUCT",
			1 => "SALELEADER",
			2 => "SPECIALOFFER",
			3 => "",
		),
		"DETAIL_PROPERTY_CODE" => array(
			0 => "NEWPRODUCT",
			1 => "MANUFACTURER",
			2 => "MATERIAL",
		),
		"LIST_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_PICTURE",
			2 => "DETAIL_PICTURE",
			3 => "",
		),
		"LIST_OFFERS_PROPERTY_CODE" => array(
			0 => "SIZES_SHOES",
			1 => "SIZES_CLOTHES",
			2 => "COLOR_REF",
			3 => "MORE_PHOTO",
			4 => "ARTNUMBER",
			5 => "",
		),
		"LIST_OFFERS_LIMIT" => "0", // Ограничивать ли количество загружаемых торговых предложений
		"DETAIL_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "",
		),
		"DETAIL_OFFERS_PROPERTY_CODE" => array(
			0 => "ARTNUMBER",
			1 => "SIZES_SHOES",
			2 => "SIZES_CLOTHES",
			3 => "COLOR_REF",
			4 => "MORE_PHOTO",
			5 => "",
		),
		// Постраничная навигация
		"PAGER_TEMPLATE" => "", // Название шаблона
		// Сравнение товаров
		"USE_COMPARE" => "N",
		// --- *** ---
		"INCLUDE_SUBSECTIONS" => "Y", // Показывать ли товары из вложенных разделов
		"USE_STORE" => "Y", // Остатки по складам
		"FIELDS" => array( // USE_STORE = Y - какие поля складов использовать
			0 => "STORE",
			1 => "SCHEDULE",
		),
		"HIDE_NOT_AVAILABLE" => "N", // Скрыть или показывать товары не в наличии
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"USE_PRICE_COUNT" => "N", // Многоуровность цен, 1шт - одна цента, 10 шт - другая и т.п.
		"SHOW_PRICE_COUNT" => "1", // Сколько уровней цен показывать для USE_PRICE_COUNT
		"PRICE_VAT_SHOW_VALUE" => "N", // Показать значение НДС
		"PRODUCT_PROPERTIES" => array( // Свойства товаров учавствующие при покупке
		),
		"CONVERT_CURRENCY" => "N", // Конвертировать валюту?
		"QUANTITY_FLOAT" => "N", // Дробное количество товаров?
		"SECTION_COUNT_ELEMENTS" => "N", // Выводить количество элементов у разделов
		// Рекомендации, апселлы
		"USE_ALSO_BUY" => "Y", // Без описания
		"ALSO_BUY_MIN_BUYES" => "1", // Минимальное количество товаров с этим товаром покупают
		"ALSO_BUY_ELEMENT_COUNT" => "4", // Количество товаров с этим товаром покупают

		// МОЖНО УДАЛИТЬ
		// может пригодиться перед удалением с вероятностью 1%
		"ADD_PICT_PROP" => "MORE_PHOTO",
		"OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
		"USE_MIN_AMOUNT" => "N", // мало, много и т.п.
		"MIN_AMOUNT" => "10", // если USE_MIN_AMOUNT = Y - порог минимального остатка
		"STORE_PATH" => "/store/#store_id#", // Нужен если есть страницы складов
		"MAIN_TITLE" => "Наличие на складах", // Заголовок блока складов

		"USE_ELEMENT_COUNTER" => "Y", // Если нужно показать количество просмотров
		"DETAIL_USE_COMMENTS" => "Y",
		"DETAIL_VOTE_DISPLAY_AS_RATING" => "rating",
		"DETAIL_USE_VOTE_RATING" => "Y",
		"DETAIL_BRAND_USE" => "Y",
		"DETAIL_BRAND_PROP_CODE" => "BRAND_REF",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"SECTION_TOP_DEPTH" => "1",
		// УДАЛИТЬ
		"PRODUCT_DISPLAY_MODE" => "Y",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_SHOW_ALWAYS" => "N",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"LIST_META_KEYWORDS" => "UF_KEYWORDS",
		"LIST_META_DESCRIPTION" => "UF_META_DESCRIPTION",
		"LIST_BROWSER_TITLE" => "UF_BROWSER_TITLE",
		"SHOW_TOP_ELEMENTS" => "N",
		"SECTIONS_SHOW_PARENT_NAME" => "N",
		"TEMPLATE_THEME" => "site",
		"SIDEBAR_SECTION_SHOW" => "Y",
		"SIDEBAR_DETAIL_SHOW" => "Y",
		"SIDEBAR_PATH" => "/catalog/sidebar.php",
		"BIG_DATA_RCM_TYPE" => "personal",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_COMPARE" => "Сравнение",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"SECTIONS_VIEW_MODE" => "TILE",
		"LINE_ELEMENT_COUNT" => "3",
		"PAGER_TITLE" => "Товары",
		"LABEL_PROP" => array(
			0 => "NEWPRODUCT",
		),
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"SECTION_BACKGROUND_IMAGE" => "UF_BACKGROUND_IMAGE",
		"DETAIL_BACKGROUND_IMAGE" => "BACKGROUND_IMAGE",
		"COMPATIBLE_MODE" => "N",
		"FILTER_VIEW_MODE" => "VERTICAL",
		"DETAIL_DISPLAY_NAME" => "N", // Показывать или нет название товара
		"DETAIL_BLOG_USE" => "Y",
		"DETAIL_VK_USE" => "N",
		"DETAIL_FB_USE" => "Y",
	),
	false
);?>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>