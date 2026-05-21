<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use Bitrix\Catalog\ProductTable;

/**
* @global CMain $APPLICATION
* @var array $arParams
* @var array $arResult
* @var CatalogSectionComponent $component
* @var CBitrixComponentTemplate $this
* @var string $templateName
* @var string $componentPath
* @var string $templateFolder
*/

$this->setFrameMode(true);

$haveOffers = !empty($arResult['OFFERS']);
$mainId = $this->GetEditAreaId($arResult['ID']);

// Карта DOM-id элементов шаблона.
// Используется в HTML и передается в jsParams['VISUAL'] для работы JCCatalogElement.
$itemIds = array(
	'ID' => $mainId, // базовый уникальный идентификатор товара на странице
	// ВИЗУАЛ \ КАРТИНКИ
	'BIG_SLIDER_ID' => $mainId.'_big_slider', // id основного большого слайдера изображений товара.
	'BIG_IMG_CONT_ID' => $mainId.'_bigimg_cont', // Контейнер для большой картинки.
	'SLIDER_CONT_ID' => $mainId.'_slider_cont', // id контейнера миниатюр слайдера для обычного товара без offers.
	'SLIDER_CONT_OF_ID' => $mainId.'_slider_cont_', // Префикс id контейнера миниатюр для каждого offer. Тут id не конечный, а заготовка
	'STICKER_ID' => $mainId.'_sticker', // id блока со стикерами/лейблами товара: хит, новинка, акция
	'DISCOUNT_PERCENT_ID' => $mainId.'_dsc_pict', // id блока, где показывается процент скидки.
	// ЦЕНА
	'OLD_PRICE_ID' => $mainId.'_old_price', // id блока со старой ценой.
	'PRICE_ID' => $mainId.'_price', // id блока с текущей основной ценой.
	'DISCOUNT_PRICE_ID' => $mainId.'_price_discount', // id блока, где показывается сумма экономии.
	'PRICE_TOTAL' => $mainId.'_price_total', // id блока итоговой суммы, зависящей от количества.
	// КОЛИЧЕСТВО
	'QUANTITY_ID' => $mainId.'_quantity', // id поля ввода количества:
	'QUANTITY_DOWN_ID' => $mainId.'_quant_down', // id кнопки минус для уменьшения количества.
	'QUANTITY_UP_ID' => $mainId.'_quant_up', // id кнопки плюс для увеличения количества.
	'QUANTITY_MEASURE' => $mainId.'_quant_measure', // id блока с единицей измерения:
	'QUANTITY_LIMIT' => $mainId.'_quant_limit', // id блока, где показывается остаток / доступное количество.
	// ПОКУПКА
	'BUY_LINK' => $mainId.'_buy_link', // id кнопки Купить. (Сразу оформить, быстрый переход)
	'ADD_BASKET_LINK' => $mainId.'_add_basket_link', // id кнопки Добавить в корзину.
	'BASKET_ACTIONS_ID' => $mainId.'_basket_actions', // id контейнера, внутри которого лежат кнопки покупки: купить, добавить. JS может скрывать этот блок
	'NOT_AVAILABLE_MESS' => $mainId.'_not_avail', // id блока с сообщением: нет в налчии, под заказ и т.п., показывается если CAN_BUY = false
	'SUBSCRIBE_LINK' => $mainId.'_subscribe', // id кнопки/контейнера подписки на поступление товара. В СВОЕМ ШАБЛОНЕ МОЖНО НЕ ИСПОЛЬЗОВАТЬ - bitrix:catalog.product.subscribe
	// SKU
	'TREE_ID' => $haveOffers && !empty($arResult['OFFERS_PROP']) ? $mainId.'_skudiv' : null, // id контейнера, где выводятся SKU-свойства. Важный блок для выбора СКУ
	'DISPLAY_PROP_DIV' => $mainId.'_sku_prop', // id контейнера, куда JS подставляет свойства выбранного offer во вкладке характеристик. Например, цве красный , размер - XL
	'DISPLAY_MAIN_PROP_DIV' => $mainId.'_main_sku_prop', // id контейнера, куда JS подставляет свойства но для основного блока характеристик рядом с карточкой, а не в общей вкладке.
	'OFFER_GROUP' => $mainId.'_set_group_', // Префикс id для блока комплектов/наборов, завязанных на конкретный offer. У каждого предложения может быть свой блок комплектов
	'BASKET_PROP_DIV' => $mainId.'_basket_prop', // id контейнера со свойствами, которые передаются в корзину.
	// ВКЛАДКИ ОПИСАНИЕ UI
	'DESCRIPTION_ID' => $mainId.'_description', // id блока с описанием товара.
	'TABS_ID' => $mainId.'_tabs', // id контейнера списка вкладок:
	'TAB_CONTAINERS_ID' => $mainId.'_tab_containers', // id контейнера, где лежит содержимое вкладок.
	'SMALL_CARD_PANEL_ID' => $mainId.'_small_card_panel', // id маленькой фиксированной карточки товара, которая появляется при прокрутке.
	'TABS_PANEL_ID' => $mainId.'_tabs_panel', // id фиксированной верхней панели вкладок, которая может появляться при скролле.
	// ПРОЧЕЕ
	'COMPARE_LINK' => $mainId.'_compare_link', // id блока/чекбокса сравнения товаров. Добавить к сравнению
);
// Будущее название переменной JS, используется один раз в конце, сохраняет в себе вызов new JCCatalogElement
$obName = $templateData['JS_OBJ'] = 'ob'.preg_replace('/[^a-zA-Z0-9_]/', 'x', $mainId);

$name = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']
	: $arResult['NAME'];
$title = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE']
	: $arResult['NAME'];
$alt = !empty($arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])
	? $arResult['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT']
	: $arResult['NAME'];

if ($haveOffers)
{
	$actualItem = $arResult['OFFERS'][$arResult['OFFERS_SELECTED']] ?? reset($arResult['OFFERS']);
	$showSliderControls = false;

	foreach ($arResult['OFFERS'] as $offer)
	{
		if ($offer['MORE_PHOTO_COUNT'] > 1)
		{
			$showSliderControls = true;
			break;
		}
	}
}
else
{
	$actualItem = $arResult;
	$showSliderControls = $arResult['MORE_PHOTO_COUNT'] > 1;
}

$skuProps = array();
$price = $actualItem['ITEM_PRICES'][$actualItem['ITEM_PRICE_SELECTED']];
$measureRatio = $actualItem['ITEM_MEASURE_RATIOS'][$actualItem['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'];
$showDiscount = $price['PERCENT'] > 0;

if ($arParams['SHOW_SKU_DESCRIPTION'] === 'Y')
{
	$skuDescription = false;
	foreach ($arResult['OFFERS'] as $offer)
	{
		if ($offer['DETAIL_TEXT'] != '' || $offer['PREVIEW_TEXT'] != '')
		{
			$skuDescription = true;
			break;
		}
	}
	$showDescription = $skuDescription || !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}
else
{
	$showDescription = !empty($arResult['PREVIEW_TEXT']) || !empty($arResult['DETAIL_TEXT']);
}
$showBuyBtn = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION']);
$buyButtonClassName = in_array('BUY', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-primary' : 'btn-link';
$showAddBtn = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION']);
$showButtonClassName = in_array('ADD', $arParams['ADD_TO_BASKET_ACTION_PRIMARY']) ? 'btn-primary' : 'btn-link';
$showSubscribe = $arParams['PRODUCT_SUBSCRIPTION'] === 'Y' && ($arResult['PRODUCT']['SUBSCRIBE'] === 'Y' || $haveOffers);

$arParams['MESS_BTN_BUY'] = $arParams['MESS_BTN_BUY'] ?: Loc::getMessage('CT_BCE_CATALOG_BUY');
$arParams['MESS_BTN_ADD_TO_BASKET'] = $arParams['MESS_BTN_ADD_TO_BASKET'] ?: Loc::getMessage('CT_BCE_CATALOG_ADD');

if ($arResult['MODULES']['catalog'] && $arResult['PRODUCT']['TYPE'] === ProductTable::TYPE_SERVICE)
{
	$arParams['~MESS_NOT_AVAILABLE_SERVICE'] ??= '';
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;

	$arParams['MESS_NOT_AVAILABLE_SERVICE'] ??= '';
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE_SERVICE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE_SERVICE')
	;
}
else
{
	$arParams['~MESS_NOT_AVAILABLE'] ??= '';
	$arParams['~MESS_NOT_AVAILABLE'] = $arParams['~MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;

	$arParams['MESS_NOT_AVAILABLE'] ??= '';
	$arParams['MESS_NOT_AVAILABLE'] = $arParams['MESS_NOT_AVAILABLE']
		?: Loc::getMessage('CT_BCE_CATALOG_NOT_AVAILABLE')
	;
}

$arParams['MESS_BTN_COMPARE'] = $arParams['MESS_BTN_COMPARE'] ?: Loc::getMessage('CT_BCE_CATALOG_COMPARE');
$arParams['MESS_PRICE_RANGES_TITLE'] = $arParams['MESS_PRICE_RANGES_TITLE'] ?: Loc::getMessage('CT_BCE_CATALOG_PRICE_RANGES_TITLE');
$arParams['MESS_DESCRIPTION_TAB'] = $arParams['MESS_DESCRIPTION_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_DESCRIPTION_TAB');
$arParams['MESS_PROPERTIES_TAB'] = $arParams['MESS_PROPERTIES_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_PROPERTIES_TAB');
$arParams['MESS_COMMENTS_TAB'] = $arParams['MESS_COMMENTS_TAB'] ?: Loc::getMessage('CT_BCE_CATALOG_COMMENTS_TAB');
$arParams['MESS_SHOW_MAX_QUANTITY'] = $arParams['MESS_SHOW_MAX_QUANTITY'] ?: Loc::getMessage('CT_BCE_CATALOG_SHOW_MAX_QUANTITY');
$arParams['MESS_RELATIVE_QUANTITY_MANY'] = $arParams['MESS_RELATIVE_QUANTITY_MANY'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_MANY');
$arParams['MESS_RELATIVE_QUANTITY_FEW'] = $arParams['MESS_RELATIVE_QUANTITY_FEW'] ?: Loc::getMessage('CT_BCE_CATALOG_RELATIVE_QUANTITY_FEW');

$positionClassMap = array(
	'left' => 'product-item-label-left',
	'center' => 'product-item-label-center',
	'right' => 'product-item-label-right',
	'bottom' => 'product-item-label-bottom',
	'middle' => 'product-item-label-middle',
	'top' => 'product-item-label-top'
);

$discountPositionClass = 'product-item-label-big';
if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($arParams['DISCOUNT_PERCENT_POSITION']))
{
	foreach (explode('-', $arParams['DISCOUNT_PERCENT_POSITION']) as $pos)
	{
		$discountPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$labelPositionClass = 'product-item-label-big';
if (!empty($arParams['LABEL_PROP_POSITION']))
{
	foreach (explode('-', $arParams['LABEL_PROP_POSITION']) as $pos)
	{
		$labelPositionClass .= isset($positionClassMap[$pos]) ? ' '.$positionClassMap[$pos] : '';
	}
}

$themeClass = isset($arParams['TEMPLATE_THEME']) ? ' bx-'.$arParams['TEMPLATE_THEME'] : '';
// НАЧАЛО ВИЗУАЛА РАЗМЕТКА + НАЧАЛЬНОЕ СОСТОЯНИЕ ИНТЕРФЕЙСА ТОВАРА И ПРИВЯЗКОЙ К ID ИЗ $itemIds (связка визуала и js) ?>
<div class="123333 bx-catalog-element<?=$themeClass?>" id="<?=$itemIds['ID']?>" itemscope itemtype="http://schema.org/Product">
	<?php
	if ($arParams['DISPLAY_NAME'] === 'Y')
	{
		?>
		<h1 class="mb-3"><?=$name?></h1>
		<?php
	}
	?>
	<div class="row">

		<div class="col-md">
			<div class="BIG_SLIDER_ID product-item-detail-slider-container" id="<?=$itemIds['BIG_SLIDER_ID']?>">
				<span class="product-item-detail-slider-close" data-entity="close-popup"></span>
				<div class="product-item-detail-slider-block
				<?=($arParams['IMAGE_RESOLUTION'] === '1by1' ? 'product-item-detail-slider-block-square' : '')?>"
					data-entity="images-slider-block">
					<span class="product-item-detail-slider-left" data-entity="slider-control-left" style="display: none;"></span>
					<span class="product-item-detail-slider-right" data-entity="slider-control-right" style="display: none;"></span>
					<div class="STICKER_ID product-item-label-text <?=$labelPositionClass?>" id="<?=$itemIds['STICKER_ID']?>"
						<?=(!$arResult['LABEL'] ? 'style="display: none;"' : '' )?>>
						<?php
						if ($arResult['LABEL'] && !empty($arResult['LABEL_ARRAY_VALUE']))
						{
							foreach ($arResult['LABEL_ARRAY_VALUE'] as $code => $value)
							{
								?>
								<div<?=(!isset($arParams['LABEL_PROP_MOBILE'][$code]) ? ' class="hidden-xs"' : '')?>>
									<span title="<?=$value?>"><?=$value?></span>
								</div>
								<?php
							}
						}
						?>
					</div>
					<?php
					if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y')
					{
						if ($haveOffers)
						{
							?>
							<div class="DISCOUNT_PERCENT_ID product-item-label-ring <?=$discountPositionClass?>"
								id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>"
								style="display: none;">
							</div>
							<?php
						}
						else
						{
							if ($price['DISCOUNT'] > 0)
							{
								?>
								<div class="DISCOUNT_PERCENT_ID product-item-label-ring <?=$discountPositionClass?>"
									id="<?=$itemIds['DISCOUNT_PERCENT_ID']?>"
									title="<?=-$price['PERCENT']?>%">
									<span><?=-$price['PERCENT']?>%</span>
								</div>
								<?php
							}
						}
					}
					?>
					<div class="product-item-detail-slider-images-container" data-entity="images-container">
						<?php
						if (!empty($actualItem['MORE_PHOTO']))
						{
							foreach ($actualItem['MORE_PHOTO'] as $key => $photo)
							{
								?>
								<div class="product-item-detail-slider-image<?=($key == 0 ? ' active' : '')?>" data-entity="image" data-id="<?=$photo['ID']?>">
									<img src="<?=$photo['SRC']?>" alt="<?=$alt?>" title="<?=$title?>"<?=($key == 0 ? ' itemprop="image"' : '')?>>
								</div>
								<?php
							}
						}

						if ($arParams['SLIDER_PROGRESS'] === 'Y')
						{
							?>
							<div class="product-item-detail-slider-progress-bar" data-entity="slider-progress-bar" style="width: 0;"></div>
							<?php
						}
						?>
					</div>
				</div>
				<?php
				if ($showSliderControls)
				{
					if ($haveOffers)
					{
						foreach ($arResult['OFFERS'] as $keyOffer => $offer)
						{
							if (!isset($offer['MORE_PHOTO_COUNT']) || $offer['MORE_PHOTO_COUNT'] <= 0)
								continue;

							$strVisible = $arResult['OFFERS_SELECTED'] == $keyOffer ? '' : 'none';
							?>
							<div class="SLIDER_CONT_OF_ID product-item-detail-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_OF_ID'].$offer['ID']?>" style="display: <?=$strVisible?>;">
								<?php
								foreach ($offer['MORE_PHOTO'] as $keyPhoto => $photo)
								{
									?>
									<div class="product-item-detail-slider-controls-image<?=($keyPhoto == 0 ? ' active' : '')?>"
										data-entity="slider-control" data-value="<?=$offer['ID'].'_'.$photo['ID']?>">
										<img src="<?=$photo['SRC']?>">
									</div>
									<?php
								}
								?>
							</div>
							<?php
						}
					}
					else
					{
						?>
						<div class="SLIDER_CONT_ID product-item-detail-slider-controls-block" id="<?=$itemIds['SLIDER_CONT_ID']?>">
							<?php
							if (!empty($actualItem['MORE_PHOTO']))
							{
								foreach ($actualItem['MORE_PHOTO'] as $key => $photo)
								{
									?>
									<div class="product-item-detail-slider-controls-image<?=($key == 0 ? ' active' : '')?>"
										data-entity="slider-control" data-value="<?=$photo['ID']?>">
										<img src="<?=$photo['SRC']?>">
									</div>
									<?php
								}
							}
							?>
						</div>
						<?php
					}
				}
				?>
			</div>
		</div>
		<?php
		$showOffersBlock = $haveOffers && !empty($arResult['OFFERS_PROP']);
		$mainBlockProperties = array_intersect_key($arResult['DISPLAY_PROPERTIES'], $arParams['MAIN_BLOCK_PROPERTY_CODE']);
		$showPropsBlock = !empty($mainBlockProperties) || $arResult['SHOW_OFFERS_PROPS'];
		$showBlockWithOffersAndProps = $showOffersBlock || $showPropsBlock;
		?>
		<div class="<?=($showBlockWithOffersAndProps ? "col-md-5 col-lg-6" : "col-md-4"); ?>">
			<div class="row">
				<?php
				if ($showBlockWithOffersAndProps)
				{
					?>
					<div class="col-lg-5">
						<?php
						foreach ($arParams['PRODUCT_INFO_BLOCK_ORDER'] as $blockName)
						{
							switch ($blockName)
							{
								case 'sku':
									if ($showOffersBlock)
									{
										?>
										<div class="mb-3 TREE_ID" id="<?=$itemIds['TREE_ID']?>">
											<?php
											foreach ($arResult['SKU_PROPS'] as $skuProperty)
											{
												if (!isset($arResult['OFFERS_PROP'][$skuProperty['CODE']]))
													continue;

												$propertyId = $skuProperty['ID'];
												$skuProps[] = array(
													'ID' => $propertyId,
													'SHOW_MODE' => $skuProperty['SHOW_MODE'],
													'VALUES' => $skuProperty['VALUES'],
													'VALUES_COUNT' => $skuProperty['VALUES_COUNT']
												);
												?>
												<div data-entity="sku-line-block" class="mb-3">
													<div class="product-item-scu-container-title"><?=htmlspecialcharsEx($skuProperty['NAME'])?></div>
													<div class="product-item-scu-container">
														<div class="product-item-scu-block">
															<div class="product-item-scu-list">
																<ul class="product-item-scu-item-list">
																	<?php
																	foreach ($skuProperty['VALUES'] as &$value)
																	{
																		$value['NAME'] = htmlspecialcharsbx($value['NAME']);

																		if ($skuProperty['SHOW_MODE'] === 'PICT')
																		{
																			?>
																			<li class="product-item-scu-item-color-container" title="<?=$value['NAME']?>"
																				data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
																				data-onevalue="<?=$value['ID']?>">
																				<div class="product-item-scu-item-color-block">
																					<div class="product-item-scu-item-color" title="<?=$value['NAME']?>"
																						style="background-image: url('<?=$value['PICT']['SRC']?>');">
																					</div>
																				</div>
																			</li>
																			<?php
																		}
																		else
																		{
																			?>
																			<li class="product-item-scu-item-text-container" title="<?=$value['NAME']?>"
																				data-treevalue="<?=$propertyId?>_<?=$value['ID']?>"
																				data-onevalue="<?=$value['ID']?>">
																				<div class="product-item-scu-item-text-block">
																					<div class="product-item-scu-item-text"><?=$value['NAME']?></div>
																				</div>
																			</li>
																			<?php
																		}
																	}
																	?>
																</ul>
																<div style="clear: both;"></div>
															</div>
														</div>
													</div>
												</div>
												<?php
											}
											?>
										</div>
										<?php
									}

									break;

								case 'props':
									if ($showPropsBlock)
									{
										?>
										<div class="mb-3">
											<?php
											if (!empty($mainBlockProperties))
											{
												?>
												<ul class="product-item-detail-properties">
													<?php
													foreach ($mainBlockProperties as $property)
													{
														?>
														<li class="product-item-detail-properties-item">
															<span class="product-item-detail-properties-name text-muted"><?=$property['NAME']?></span>
															<span class="product-item-detail-properties-dots"></span>
															<span class="product-item-detail-properties-value"><?=(is_array($property['DISPLAY_VALUE'])
																	? implode(' / ', $property['DISPLAY_VALUE'])
																	: $property['DISPLAY_VALUE'])?>
													</span>
														</li>
														<?php
													}
													?>
												</ul>
												<?php
											}

											if ($arResult['SHOW_OFFERS_PROPS'])
											{
												?>
												<ul class="DISPLAY_MAIN_PROP_DIV product-item-detail-properties" id="<?=$itemIds['DISPLAY_MAIN_PROP_DIV']?>"></ul>
												<?php
											}
											?>
										</div>
										<?php
									}

									break;
							}
						}
						?>
					</div>
					<?php
				}
				?>
				<div class="<?=($showBlockWithOffersAndProps ? "col-lg-7" : "col-lg"); ?>">
					<div class="product-item-detail-pay-block">
						<?php
						foreach ($arParams['PRODUCT_PAY_BLOCK_ORDER'] as $blockName)
						{
							switch ($blockName)
							{
								case 'price':
									?>
									<div class="mb-3">
										<?php
										if ($arParams['SHOW_OLD_PRICE'] === 'Y')
										{
											?>
											<div class="OLD_PRICE_ID product-item-detail-price-old mb-1"
												id="<?=$itemIds['OLD_PRICE_ID']?>"
												<?=($showDiscount ? '' : 'style="display: none;"')?>><?=($showDiscount ? $price['PRINT_RATIO_BASE_PRICE'] : '')?></div>
											<?php
										}
										?>

										<div class="PRICE_ID product-item-detail-price-current mb-1" id="<?=$itemIds['PRICE_ID']?>"><?=$price['PRINT_RATIO_PRICE']?></div>

										<?php
										if ($arParams['SHOW_OLD_PRICE'] === 'Y')
										{
											?>
											<div class="DISCOUNT_PRICE_ID product-item-detail-economy-price mb-1"
												id="<?=$itemIds['DISCOUNT_PRICE_ID']?>"
												<?=($showDiscount ? '' : 'style="display: none;"')?>><?php
												if ($showDiscount)
												{
													echo Loc::getMessage('CT_BCE_CATALOG_ECONOMY_INFO2', array('#ECONOMY#' => $price['PRINT_RATIO_DISCOUNT']));
												}
												?></div>
											<?php
										}
										?>
									</div>
									<?php
									break;

									// Можно вырезать если не будет диапазона цен
								case 'priceRanges':
									if ($arParams['USE_PRICE_COUNT'])
									{
										$showRanges = !$haveOffers && count($actualItem['ITEM_QUANTITY_RANGES']) > 1;
										$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';
										?>
										<div class="mb-3"
											<?=$showRanges ? '' : 'style="display: none;"'?>
											data-entity="price-ranges-block">
											<?php
											if ($arParams['MESS_PRICE_RANGES_TITLE'])
											{
												?>
												<div class="product-item-detail-info-container-title text-center">
													<?= $arParams['MESS_PRICE_RANGES_TITLE'] ?>
													<span data-entity="price-ranges-ratio-header">
												(<?= (Loc::getMessage(
															'CT_BCE_CATALOG_RATIO_PRICE',
															array('#RATIO#' => ($useRatio ? $measureRatio : '1').' '.$actualItem['ITEM_MEASURE']['TITLE'])
														)) ?>)
											</span>
												</div>
												<?php
											}
											?>
											<ul class="product-item-detail-properties" data-entity="price-ranges-body">
												<?php
												if ($showRanges)
												{
													foreach ($actualItem['ITEM_QUANTITY_RANGES'] as $range)
													{
														if ($range['HASH'] !== 'ZERO-INF')
														{
															$itemPrice = false;

															foreach ($arResult['ITEM_PRICES'] as $itemPrice)
															{
																if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
																{
																	break;
																}
															}

															if ($itemPrice)
															{
																?>
																<li class="product-item-detail-properties-item">
																<span class="product-item-detail-properties-name text-muted">
																	<?php
																	echo Loc::getMessage(
																			'CT_BCE_CATALOG_RANGE_FROM',
																			array('#FROM#' => $range['SORT_FROM'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
																		).' ';

																	if (is_infinite($range['SORT_TO']))
																	{
																		echo Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
																	}
																	else
																	{
																		echo Loc::getMessage(
																			'CT_BCE_CATALOG_RANGE_TO',
																			array('#TO#' => $range['SORT_TO'].' '.$actualItem['ITEM_MEASURE']['TITLE'])
																		);
																	}
																	?>
																</span>
																	<span class="product-item-detail-properties-dots"></span>
																	<span class="product-item-detail-properties-value"><?=($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE'])?></span>
																</li>
																<?php
															}
														}
													}
												}
												?>
											</ul>
										</div>
										<?php
										unset($showRanges, $useRatio, $itemPrice, $range);
									}

									break;
								// Можно вырезать если не будет остаток/“много-мало”
								case 'quantityLimit':
									if ($arParams['SHOW_MAX_QUANTITY'] !== 'N')
									{
										if ($haveOffers)
										{
											?>
											<div class="mb-3 QUANTITY_LIMIT" id="<?=$itemIds['QUANTITY_LIMIT']?>" style="display: none;">
												<div class="product-item-detail-info-container-title text-center">
													<?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:
												</div>
												<span class="product-item-quantity" data-entity="quantity-limit-value"></span>
											</div>
											<?php
										}
										else
										{
											if (
												$measureRatio
												&& (float)$actualItem['PRODUCT']['QUANTITY'] > 0
												&& $actualItem['CHECK_QUANTITY']
											)
											{
												?>
												<div class="mb-3 text-center QUANTITY_LIMIT" id="<?=$itemIds['QUANTITY_LIMIT']?>">
													<span class="product-item-detail-info-container-title"><?=$arParams['MESS_SHOW_MAX_QUANTITY']?>:</span>
													<span class="product-item-quantity" data-entity="quantity-limit-value">
													<?php
													if ($arParams['SHOW_MAX_QUANTITY'] === 'M')
													{
														if ((float)$actualItem['PRODUCT']['QUANTITY'] / $measureRatio >= $arParams['RELATIVE_QUANTITY_FACTOR'])
														{
															echo $arParams['MESS_RELATIVE_QUANTITY_MANY'];
														}
														else
														{
															echo $arParams['MESS_RELATIVE_QUANTITY_FEW'];
														}
													}
													else
													{
														echo $actualItem['PRODUCT']['QUANTITY'].' '.$actualItem['ITEM_MEASURE']['TITLE'];
													}
													?>
												</span>
												</div>
												<?php
											}
										}
									}

									break;

								case 'quantity':
									if ($arParams['USE_PRODUCT_QUANTITY'])
									{
										?>
										<div class="mb-3" <?= (!$actualItem['CAN_BUY'] ? ' style="display: none;"' : '') ?> data-entity="quantity-block">
											<?php
											if (Loc::getMessage('CATALOG_QUANTITY'))
											{
												?>
												<div class="product-item-detail-info-container-title text-center"><?= Loc::getMessage('CATALOG_QUANTITY') ?></div>
												<?php
											}
											?>

											<div class="product-item-amount">
												<div class="product-item-amount-field-container">
													<span class="QUANTITY_DOWN_ID product-item-amount-field-btn-minus no-select" id="<?=$itemIds['QUANTITY_DOWN_ID']?>"></span>
													<div class="product-item-amount-field-block">
														<input class="QUANTITY_ID product-item-amount-field" id="<?=$itemIds['QUANTITY_ID']?>" type="number" value="<?=$price['MIN_QUANTITY']?>">
														<span class="product-item-amount-description-container">
														<span class="QUANTITY_MEASURE" id="<?=$itemIds['QUANTITY_MEASURE']?>"><?=$actualItem['ITEM_MEASURE']['TITLE']?></span>
														<span class="PRICE_TOTAL" id="<?=$itemIds['PRICE_TOTAL']?>"></span>
													</span>
													</div>
													<span class="QUANTITY_UP_ID product-item-amount-field-btn-plus no-select" id="<?=$itemIds['QUANTITY_UP_ID']?>"></span>
												</div>
											</div>
										</div>
										<?php
									}

									break;

								case 'buttons':
									?>
									<div data-entity="main-button-container" class="mb-3">
										<div class="BASKET_ACTIONS_ID" id="<?=$itemIds['BASKET_ACTIONS_ID']?>" style="display: <?=($actualItem['CAN_BUY'] ? '' : 'none')?>;">
											<?php
											if ($showAddBtn)
											{
												?>
												<div class="mb-3">
													<a class="ADD_BASKET_LINK btn <?=$showButtonClassName?> product-item-detail-buy-button"
														id="<?=$itemIds['ADD_BASKET_LINK']?>"
														href="javascript:void(0);">
														<?=$arParams['MESS_BTN_ADD_TO_BASKET']?>
													</a>
												</div>
												<?php
											}

											if ($showBuyBtn)
											{
												?>
												<div class="mb-3">
													<a class="BUY_LINK btn <?=$buyButtonClassName?> product-item-detail-buy-button"
														id="<?=$itemIds['BUY_LINK']?>"
														href="javascript:void(0);">
														<?=$arParams['MESS_BTN_BUY']?>
													</a>
												</div>
												<?php
											}
											?>
										</div>
									</div>
									<div class="NOT_AVAILABLE_MESS mb-3" id="<?=$itemIds['NOT_AVAILABLE_MESS']?>" style="display: <?=(!$actualItem['CAN_BUY'] ? '' : 'none')?>;">
										<a class="btn btn-primary product-item-detail-buy-button" href="javascript:void(0)" rel="nofollow"><?=$arParams['MESS_NOT_AVAILABLE']?></a>
									</div>
									<?php
									break;
							}
						}

						if ($arParams['DISPLAY_COMPARE'])
						{
							?>
							<div class="product-item-detail-compare-container">
								<div class="product-item-detail-compare">
									<div class="checkbox">
										<label class="COMPARE_LINK m-0" id="<?=$itemIds['COMPARE_LINK']?>">
											<input type="checkbox" data-entity="compare-checkbox">
											<span data-entity="compare-title"><?=$arParams['MESS_BTN_COMPARE']?></span>
										</label>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>

	</div>

	<?php
	if ($haveOffers)
	{
		foreach ($arResult['JS_OFFERS'] as $offer)
		{
			$currentOffersList = array();

			if (!empty($offer['TREE']) && is_array($offer['TREE']))
			{
				foreach ($offer['TREE'] as $propName => $skuId)
				{
					$propId = (int)substr($propName, 5);

					foreach ($skuProps as $prop)
					{
						if ($prop['ID'] == $propId)
						{
							foreach ($prop['VALUES'] as $propId => $propValue)
							{
								if ($propId == $skuId)
								{
									$currentOffersList[] = $propValue['NAME'];
									break;
								}
							}
						}
					}
				}
			}

			$offerPrice = $offer['ITEM_PRICES'][$offer['ITEM_PRICE_SELECTED']];
			?>
			<?php
		}

		unset($offerPrice, $currentOffersList);
	}
	else
	{
		?>
		<?php
	}
	// Конец HTML-разметки карточки ?>

	<?php // Подготовка данных и HTML-фрагментов для JS-компонента JCCatalogElement
	if ($haveOffers)
	{ // $jsParams - формируется если товар имеет торговые предложения
		$offerIds = array();
		$offerCodes = array();

		$useRatio = $arParams['USE_RATIO_IN_RANGES'] === 'Y';

		foreach ($arResult['JS_OFFERS'] as $ind => &$jsOffer)
		{
			$offerIds[] = (int)$jsOffer['ID'];
			$offerCodes[] = $jsOffer['CODE'];

			$fullOffer = $arResult['OFFERS'][$ind];
			$measureName = $fullOffer['ITEM_MEASURE']['TITLE'];

			$strAllProps = '';
			$strMainProps = '';
			$strPriceRangesRatio = '';
			$strPriceRanges = '';

			if ($arResult['SHOW_OFFERS_PROPS'])
			{
				if (!empty($jsOffer['DISPLAY_PROPERTIES']))
				{
					foreach ($jsOffer['DISPLAY_PROPERTIES'] as $property)
					{
						$current = '<li class="product-item-detail-properties-item">
					<span class="product-item-detail-properties-name">'.$property['NAME'].'</span>
					<span class="product-item-detail-properties-dots"></span>
					<span class="product-item-detail-properties-value">'.(
							is_array($property['VALUE'])
								? implode(' / ', $property['VALUE'])
								: $property['VALUE']
							).'</span></li>';
						$strAllProps .= $current;

						if (isset($arParams['MAIN_BLOCK_OFFERS_PROPERTY_CODE'][$property['CODE']]))
						{
							$strMainProps .= $current;
						}
					}

					unset($current);
				}
			}

			if ($arParams['USE_PRICE_COUNT'] && count($jsOffer['ITEM_QUANTITY_RANGES']) > 1)
			{
				$strPriceRangesRatio = '('.Loc::getMessage(
						'CT_BCE_CATALOG_RATIO_PRICE',
						array('#RATIO#' => ($useRatio
								? $fullOffer['ITEM_MEASURE_RATIOS'][$fullOffer['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']
								: '1'
							).' '.$measureName)
					).')';

				foreach ($jsOffer['ITEM_QUANTITY_RANGES'] as $range)
				{
					if ($range['HASH'] !== 'ZERO-INF')
					{
						$itemPrice = false;

						foreach ($jsOffer['ITEM_PRICES'] as $itemPrice)
						{
							if ($itemPrice['QUANTITY_HASH'] === $range['HASH'])
							{
								break;
							}
						}

						if ($itemPrice)
						{
							$strPriceRanges .= '<dt>'.Loc::getMessage(
									'CT_BCE_CATALOG_RANGE_FROM',
									array('#FROM#' => $range['SORT_FROM'].' '.$measureName)
								).' ';

							if (is_infinite($range['SORT_TO']))
							{
								$strPriceRanges .= Loc::getMessage('CT_BCE_CATALOG_RANGE_MORE');
							}
							else
							{
								$strPriceRanges .= Loc::getMessage(
									'CT_BCE_CATALOG_RANGE_TO',
									array('#TO#' => $range['SORT_TO'].' '.$measureName)
								);
							}

							$strPriceRanges .= '</dt><dd>'.($useRatio ? $itemPrice['PRINT_RATIO_PRICE'] : $itemPrice['PRINT_PRICE']).'</dd>';
						}
					}
				}

				unset($range, $itemPrice);
			}

			// Здесь PHP дополняет JS_OFFERS HTML-фрагментами, чтобы JS потом не строил их вручную
			$jsOffer['DISPLAY_PROPERTIES'] = $strAllProps; // HTML списка свойств для вкладки
			$jsOffer['DISPLAY_PROPERTIES_MAIN_BLOCK'] = $strMainProps; // HTML свойств для основного блока
			$jsOffer['PRICE_RANGES_RATIO_HTML'] = $strPriceRangesRatio; // подпись с коэффициентом измерения
			$jsOffer['PRICE_RANGES_HTML'] = $strPriceRanges; // HTML диапазонов цен
		}

		$templateData['OFFER_IDS'] = $offerIds;
		$templateData['OFFER_CODES'] = $offerCodes;
		unset($jsOffer, $strAllProps, $strMainProps, $strPriceRanges, $strPriceRangesRatio, $useRatio);

		$jsParams = array(
			'CONFIG' => array( // общий конфиг поведения JS-компонента
				'USE_CATALOG' => $arResult['CATALOG'], // bool карточка работает в режиме каталога
				'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'], // bool Нужно ли JS работать с блоком количества: плюс/минус пересчет суммы поле ввода
				'SHOW_PRICE' => true, // Нужно ли показывать и обновлять цену. Для offers стоит true, потому что цена там обязательный рабочий сценарий. Для обычного товара Bitrix проверяет, есть ли вообще массив цен.
				'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y', // Нужно ли показывать блок процента скидки: -10% -25% Если false, JS даже не будет пытаться обновлять этот элемент.
				'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y', // Нужно ли работать со старой ценой:
				'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'], // Включен ли режим диапазонов цен по количеству. от 1 шт — 500 ₽ от 10 шт — 450 ₽ от 50 шт — 400 ₽
				'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'], // Включен ли функционал сравнения товаров. Если да, JS обслуживает чекбокс “сравнить”.
				'SHOW_SKU_PROPS' => $arResult['SHOW_OFFERS_PROPS'], // Есть только в ветке offers. Показывать ли свойства выбранного offer: цвет размер Имеется в виду именно их динамический вывод в карточке.
				'OFFER_GROUP' => $arResult['OFFER_GROUP'], // Нужно ли обслуживать блок комплектов/наборов, связанных с конкретным offer.
				'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'], // Режим работы главной картинки. Влияет на то, как JS обновляет изображения: использовать детальную, использовать превью, как строить галерею.
				'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'], // Какие действия доступны: ADD BUY или оба варианта JS понимает, какую кнопку обслуживать и какой сценарий вызывать.
				'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y', // Показывать ли кнопку закрытия popup после действий типа “добавлено в корзину”.
				'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'], // Как работать с остатком: не показывать, показывать число, показывать “много/мало”
				'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'], // Порог для режима “много / мало”.
				'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'], // МУСОР Используется JS/шаблоном для стилистической совместимости.
				'USE_STICKERS' => true, // Нужно ли работать со стикерами товара: Хит Новинка Акция
				'USE_SUBSCRIBE' => $showSubscribe, // Нужен ли функционал подписки на поступление. JS учитывает, что для недоступного товара можно показать подписку вместо покупки.
				'SHOW_SLIDER' => $arParams['SHOW_SLIDER'], // Нужно ли обслуживать слайдер изображений.
				'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'], // Интервал автопрокрутки слайдера, если включена такая механика.
				'ALT' => $alt, // alt для картинок, который JS использует при подмене изображений.
				'TITLE' => $title, // title для картинок, тоже нужен при динамической замене.
				'MAGNIFIER_ZOOM_PERCENT' => 200, // Процент увеличения для лупы/зумера изображения.
				'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'], // Включена ли enhanced ecommerce аналитика.
				'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'], // Имя JS data layer для аналитики. Например для отправки событий в GTM.
				'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]) // Значение бренда товара. Используется обычно в аналитике/ecommerce-событиях.
					? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
					: null,
				'SHOW_SKU_DESCRIPTION' => $arParams['SHOW_SKU_DESCRIPTION'], // Есть только в ветке offers. Можно ли подменять описание при переключении offer.
				'DISPLAY_PREVIEW_TEXT_MODE' => $arParams['DISPLAY_PREVIEW_TEXT_MODE'] // Есть только в ветке offers. Говорит JS, как работать с preview/detail text при смене предложения.
			),
			'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'], // Тип продукта из каталога. Нужен JS для правильной логики покупки и отображения.
			'VISUAL' => $itemIds, // Здесь передается карта DOM-id элементов
			'DEFAULT_PICTURE' => array( // Есть только в ветке offers. Это запасная картинка, если у выбранного offer нет своей.
				'PREVIEW_PICTURE' => $arResult['DEFAULT_PICTURE'],
				'DETAIL_PICTURE' => $arResult['DEFAULT_PICTURE']
			),
			'PRODUCT' => array( // В режиме offers PRODUCT содержит базовый товар, а не полное состояние цены и т.п. Потому что реальные цена/фото/остатки живут уже на уровне OFFERS
				'ID' => $arResult['ID'], // ID базового товара.
				'ACTIVE' => $arResult['ACTIVE'], // Активен ли товар.
				'NAME' => $arResult['~NAME'], // Название товара.
				'CATEGORY' => $arResult['CATEGORY_PATH'], // Путь категории/раздела.
				'DETAIL_TEXT' => $arResult['DETAIL_TEXT'], // Полное описание товара.
				'DETAIL_TEXT_TYPE' => $arResult['DETAIL_TEXT_TYPE'], // Тип полного описания: text html
				'PREVIEW_TEXT' => $arResult['PREVIEW_TEXT'], // Краткое описание.
				'PREVIEW_TEXT_TYPE' => $arResult['PREVIEW_TEXT_TYPE'] // Тип краткого описания: text html
			), // В режиме offers это нужно, чтобы JS мог: подставлять базовое описание, менять его при необходимости, работать с аналитикой/названием/категорией.
			'BASKET' => array( // Это секция для логики добавления в корзину.
				'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'], // Имя параметра количества, которое уйдет в запрос. Например quantity
				'BASKET_URL' => $arParams['BASKET_URL'], // URL корзины.
				'SKU_PROPS' => $arResult['OFFERS_PROP_CODES'], // Коды SKU-свойств, которые надо учитывать при покупке: COLOR SIZE
				'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'], // Шаблон URL для “добавить в корзину”.
				'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'] // Шаблон URL для “купить”.
			),
			'OFFERS' => $arResult['JS_OFFERS'], // Есть только при offers. Это сердце SKU-логики. Именно этот массив JS перебирает при выборе варианта.
			'OFFER_SELECTED' => $arResult['OFFERS_SELECTED'], // Индекс выбранного по умолчанию offer. Чтобы JS знал, какое предложение сначала показать.
			'TREE_PROPS' => $skuProps // Есть только при offers. Это описание самих SKU-свойств:
		);
	}
	else
	{ // $jsParams - формируется для одиночного товара
		$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
		if ($arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y' && !$emptyProductProperties)
		{
			?>
			<div class="BASKET_PROP_DIV" id="<?=$itemIds['BASKET_PROP_DIV']?>" style="display: none;">
				<?php // у обычного товара без offers могут быть свойства, которые надо выбрать перед добавлением в корзину: размер комплектации, опция упаковки, тип исполнения, дополнительные параметры.
				if (!empty($arResult['PRODUCT_PROPERTIES_FILL']))
				{
					foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propId => $propInfo)
					{
						?>
						<input type="hidden" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]" value="<?=htmlspecialcharsbx($propInfo['ID'])?>">
						<?php
						unset($arResult['PRODUCT_PROPERTIES'][$propId]);
					}
				}

				$emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
				if (!$emptyProductProperties)
				{
					?>
					<table>
						<?php
						foreach ($arResult['PRODUCT_PROPERTIES'] as $propId => $propInfo)
						{
							?>
							<tr>
								<td><?=$arResult['PROPERTIES'][$propId]['NAME']?></td>
								<td>
									<?php
									if (
										$arResult['PROPERTIES'][$propId]['PROPERTY_TYPE'] === 'L'
										&& $arResult['PROPERTIES'][$propId]['LIST_TYPE'] === 'C'
									)
									{
										foreach ($propInfo['VALUES'] as $valueId => $value)
										{
											?>
											<label>
												<input type="radio" name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]"
													value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? 'checked' : '')?>>
												<?=$value?>
											</label>
											<br>
											<?php
										}
									}
									else
									{
										?>
										<select name="<?=$arParams['PRODUCT_PROPS_VARIABLE']?>[<?=$propId?>]">
											<?php
											foreach ($propInfo['VALUES'] as $valueId => $value)
											{
												?>
												<option value="<?=$valueId?>" <?=($valueId == $propInfo['SELECTED'] ? 'selected' : '')?>>
													<?=$value?>
												</option>
												<?php
											}
											?>
										</select>
										<?php
									}
									?>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
					<?php
				}
				?>
			</div>
			<?php
		}

		$jsParams = array(
			'CONFIG' => array(
				'USE_CATALOG' => $arResult['CATALOG'],
				'SHOW_QUANTITY' => $arParams['USE_PRODUCT_QUANTITY'],
				'SHOW_PRICE' => !empty($arResult['ITEM_PRICES']),
				'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'] === 'Y',
				'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'] === 'Y',
				'USE_PRICE_COUNT' => $arParams['USE_PRICE_COUNT'],
				'DISPLAY_COMPARE' => $arParams['DISPLAY_COMPARE'],
				'MAIN_PICTURE_MODE' => $arParams['DETAIL_PICTURE_MODE'],
				'ADD_TO_BASKET_ACTION' => $arParams['ADD_TO_BASKET_ACTION'],
				'SHOW_CLOSE_POPUP' => $arParams['SHOW_CLOSE_POPUP'] === 'Y',
				'SHOW_MAX_QUANTITY' => $arParams['SHOW_MAX_QUANTITY'],
				'RELATIVE_QUANTITY_FACTOR' => $arParams['RELATIVE_QUANTITY_FACTOR'],
				'TEMPLATE_THEME' => $arParams['TEMPLATE_THEME'],
				'USE_STICKERS' => true,
				'USE_SUBSCRIBE' => $showSubscribe,
				'SHOW_SLIDER' => $arParams['SHOW_SLIDER'],
				'SLIDER_INTERVAL' => $arParams['SLIDER_INTERVAL'],
				'ALT' => $alt,
				'TITLE' => $title,
				'MAGNIFIER_ZOOM_PERCENT' => 200,
				'USE_ENHANCED_ECOMMERCE' => $arParams['USE_ENHANCED_ECOMMERCE'],
				'DATA_LAYER_NAME' => $arParams['DATA_LAYER_NAME'],
				'BRAND_PROPERTY' => !empty($arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']])
					? $arResult['DISPLAY_PROPERTIES'][$arParams['BRAND_PROPERTY']]['DISPLAY_VALUE']
					: null
			),
			'VISUAL' => $itemIds,
			'PRODUCT_TYPE' => $arResult['PRODUCT']['TYPE'],
			'PRODUCT' => array( // ветка без offers
				'ID' => $arResult['ID'], // ID товара.
				'ACTIVE' => $arResult['ACTIVE'], // Активен ли товар.
				'PICT' => reset($arResult['MORE_PHOTO']), // Главная картинка товара.
				'NAME' => $arResult['~NAME'], // Название.
				'SUBSCRIPTION' => true, // Флаг, что подписка в принципе поддерживается для этой карточки.
				'ITEM_PRICE_MODE' => $arResult['ITEM_PRICE_MODE'], // Режим отображения цен. Например, как именно интерпретировать набор цен и диапазонов.
				'ITEM_PRICES' => $arResult['ITEM_PRICES'], // Массив всех цен товара. Это один из главных массивов для расчета и подмены цен.
				'ITEM_PRICE_SELECTED' => $arResult['ITEM_PRICE_SELECTED'], // Индекс выбранной текущей цены из ITEM_PRICES JS понимает, какая цена сейчас активна.
				'ITEM_QUANTITY_RANGES' => $arResult['ITEM_QUANTITY_RANGES'], // Диапазоны количества для цен. 1–9 10–49 50+
				'ITEM_QUANTITY_RANGE_SELECTED' => $arResult['ITEM_QUANTITY_RANGE_SELECTED'], // Текущий выбранный диапазон количества.
				'ITEM_MEASURE_RATIOS' => $arResult['ITEM_MEASURE_RATIOS'], // Массив коэффициентов измерения. Например: 1 шт 0.5 кг 10 м
				'ITEM_MEASURE_RATIO_SELECTED' => $arResult['ITEM_MEASURE_RATIO_SELECTED'], // Текущий выбранный коэффициент измерения.
				'SLIDER_COUNT' => $arResult['MORE_PHOTO_COUNT'], // Количество изображений.
				'SLIDER' => $arResult['MORE_PHOTO'], // Массив картинок товара для слайдера.
				'CAN_BUY' => $arResult['CAN_BUY'], // Можно ли купить товар. JS по этому флагу решает: показывать кнопки покупки, показывать “нет в наличии”, использовать подписку.
				'CHECK_QUANTITY' => $arResult['CHECK_QUANTITY'], // Нужно ли проверять остаток.
				'QUANTITY_FLOAT' => is_float($arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO']), // Разрешены ли дробные количества. Например: 1.5 кг — да 2.75 м — да 3 шт — обычно нет
				'MAX_QUANTITY' => $arResult['PRODUCT']['QUANTITY'], // Максимально доступный остаток.
				'STEP_QUANTITY' => $arResult['ITEM_MEASURE_RATIOS'][$arResult['ITEM_MEASURE_RATIO_SELECTED']]['RATIO'], // Шаг количества. Например: по 1 шт по 0.5 кг по 10 метров
				'CATEGORY' => $arResult['CATEGORY_PATH'] // Категория товара.
			),
			'BASKET' => array( // Это секция для логики добавления в корзину.
				'ADD_PROPS' => $arParams['ADD_PROPERTIES_TO_BASKET'] === 'Y', // Нужно ли передавать свойства товара в корзину.
				'QUANTITY' => $arParams['PRODUCT_QUANTITY_VARIABLE'], // Имя параметра количества.
				'PROPS' => $arParams['PRODUCT_PROPS_VARIABLE'], // Имя параметра для пользовательских свойств товара.
				'EMPTY_PROPS' => $emptyProductProperties, // Есть ли вообще свойства для выбора перед покупкой. Если false, JS может попросить пользователя заполнить свойства.
				'BASKET_URL' => $arParams['BASKET_URL'], // URL корзины.
				'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'], // Шаблон URL для “добавить в корзину”.
				'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'] // Шаблон URL для “купить”.
			)
		);
		unset($emptyProductProperties);
	}

	// Убирать если нет сравнения
	if ($arParams['DISPLAY_COMPARE']) // Добавляется отдельно, если включено сравнение
	{
		$jsParams['COMPARE'] = array(
			'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'], // Шаблон URL для добавления товара в сравнение.
			'COMPARE_DELETE_URL_TEMPLATE' => $arResult['~COMPARE_DELETE_URL_TEMPLATE'], // Шаблон URL для удаления из сравнения.
			'COMPARE_PATH' => $arParams['COMPARE_PATH'] // URL страницы сравнения.
		);
	}

	// Флаг для интеграции с Facebook conversion / analytics событиями. JS использует его для дополнительной маркетинговой аналитики.
	$jsParams["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"] =
		$arResult["IS_FACEBOOK_CONVERSION_CUSTOMIZE_PRODUCT_EVENT_ENABLED"]
	;
	?>
</div>
<script>
	BX.message({
		ECONOMY_INFO_MESSAGE: '<?=GetMessageJS('CT_BCE_CATALOG_ECONOMY_INFO2')?>',
		TITLE_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_ERROR')?>',
		TITLE_BASKET_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_TITLE_BASKET_PROPS')?>',
		BASKET_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_BASKET_UNKNOWN_ERROR')?>',
		BTN_SEND_PROPS: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_SEND_PROPS')?>',
		BTN_MESSAGE_DETAIL_BASKET_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_BASKET_REDIRECT')?>',
		BTN_MESSAGE_CLOSE: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE')?>',
		BTN_MESSAGE_DETAIL_CLOSE_POPUP: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_CLOSE_POPUP')?>',
		TITLE_SUCCESSFUL: '<?=GetMessageJS('CT_BCE_CATALOG_ADD_TO_BASKET_OK')?>',
		COMPARE_MESSAGE_OK: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_OK')?>',
		COMPARE_UNKNOWN_ERROR: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_UNKNOWN_ERROR')?>',
		COMPARE_TITLE: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_COMPARE_TITLE')?>',
		BTN_MESSAGE_COMPARE_REDIRECT: '<?=GetMessageJS('CT_BCE_CATALOG_BTN_MESSAGE_COMPARE_REDIRECT')?>',
		PRODUCT_GIFT_LABEL: '<?=GetMessageJS('CT_BCE_CATALOG_PRODUCT_GIFT_LABEL')?>',
		PRICE_TOTAL_PREFIX: '<?=GetMessageJS('CT_BCE_CATALOG_MESS_PRICE_TOTAL_PREFIX')?>',
		RELATIVE_QUANTITY_MANY: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_MANY'])?>',
		RELATIVE_QUANTITY_FEW: '<?=CUtil::JSEscape($arParams['MESS_RELATIVE_QUANTITY_FEW'])?>',
		SITE_ID: '<?=CUtil::JSEscape($component->getSiteId())?>'
	});

	var <?=$obName?> = new JCCatalogElement(<?=CUtil::PhpToJSObject($jsParams, false, true)?>);
	console.log(<?=$obName?>)
</script>
<?php

//echo '<!--  BXDEBUG$actualItem: <pre>' . print_r($actualItem, 1) . '</pre>-->';
//echo '<!--  BXDEBUG$itemIds: <pre>' . print_r($itemIds, 1) . '</pre>-->';
echo '<!--  BXDEBUG$jsParams: <pre>' . print_r($jsParams, 1) . '</pre>-->';
//echo '<!--  BXDEBUG$arResult: <pre>' . print_r($arResult, 1) . '</pre>-->';
unset($actualItem, $itemIds, $jsParams);
