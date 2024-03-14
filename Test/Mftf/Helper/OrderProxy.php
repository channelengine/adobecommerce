<?php

declare(strict_types=1);

namespace ChannelEngine\ChannelEngineIntegration\Test\Mftf\Helper;

use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\OrdersPage;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\DTO\Responses\AcknowledgeResponse;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\BusinessLogic\API\Orders\Http\Proxy;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\Configuration\ConfigurationManager;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ORM\Exceptions\QueryFilterInvalidParamException;
use ChannelEngine\ChannelEngineIntegration\IntegrationCore\Infrastructure\ServiceRegister;
use DateTime;

/**
 * Class OrderProxy
 *
 * @package  ChannelEngine\ChannelEngineIntegration\Test\Mftf\Helper
 */
class OrderProxy extends Proxy
{
    /**
     * @var bool
     */
    private $acknowledged = false;

    /**
     * @inheirtDoc
     */
    public function getNew()
    {
        $response = [
            "Content" => $this->acknowledged ? [] : [
                [
                    "Id" => 973,
                    "ChannelName" => "AliExpress",
                    "ChannelId" => 5,
                    "GlobalChannelName" => "AliExpress",
                    "GlobalChannelId" => 1515,
                    "ChannelOrderSupport" => "ORDERS",
                    "ChannelOrderNo" => "CE-TEST-MFTF-1",
                    "CommercialOrderNo" => "CE-TEST-MFTF-1",
                    "MerchantOrderNo" => null,
                    "Status" => "NEW",
                    "IsBusinessOrder" => false,
                    "AcknowledgedDate" => null,
                    "CreatedAt" => "2024-02-15T17:14:40.3535111+01:00",
                    "UpdatedAt" => "2024-02-15T17:14:40.3535111+01:00",
                    "MerchantComment" => null,
                    "BillingAddress" => [
                        "Line1" => "Teststreet 22",
                        "Line2" => null,
                        "Line3" => null,
                        "Gender" => "NOT_APPLICABLE",
                        "CompanyName" => null,
                        "FirstName" => "T.",
                        "LastName" => "Tester",
                        "StreetName" => "Teststreet",
                        "HouseNr" => "22",
                        "HouseNrAddition" => null,
                        "ZipCode" => "1111 TT",
                        "City" => "Testtown",
                        "Region" => null,
                        "CountryIso" => "NL",
                        "Original" => "Teststreet 22"
                    ],
                    "ShippingAddress" => [
                        "Line1" => "Teststreet 22",
                        "Line2" => null,
                        "Line3" => null,
                        "Gender" => "NOT_APPLICABLE",
                        "CompanyName" => null,
                        "FirstName" => "T.",
                        "LastName" => "Tester",
                        "StreetName" => "Teststreet",
                        "HouseNr" => "22",
                        "HouseNrAddition" => null,
                        "ZipCode" => "1111 TT",
                        "City" => "Testtown",
                        "Region" => null,
                        "CountryIso" => "NL",
                        "Original" => "Teststreet 22"
                    ],
                    "SubTotalInclVat" => 1,
                    "SubTotalVat" => 0.17,
                    "ShippingCostsVat" => 0,
                    "TotalInclVat" => 1,
                    "TotalVat" => 0.17,
                    "OriginalSubTotalInclVat" => 1,
                    "OriginalSubTotalVat" => 0.17,
                    "OriginalShippingCostsInclVat" => 0,
                    "OriginalShippingCostsVat" => 0,
                    "OriginalTotalInclVat" => 1,
                    "OriginalTotalVat" => 0.17,
                    "SubTotalExclVat" => 0.83,
                    "TotalExclVat" => 0.83,
                    "ShippingCostsExclVat" => 0,
                    "OriginalSubTotalExclVat" => 0.83,
                    "OriginalShippingCostsExclVat" => 0,
                    "OriginalTotalExclVat" => 0.83,
                    "Lines" => [
                        [
                            "Id" => 1043,
                            "ChannelOrderLineNo" => null,
                            "Status" => "NEW",
                            "IsFulfillmentByMarketplace" => false,
                            "Gtin" => "1",
                            "Description" => "productsync",
                            "StockLocation" => [
                                "Id" => 1,
                                "Name" => "Logeecom 2"
                            ],
                            "UnitVat" => 0.17,
                            "LineTotalInclVat" => 1,
                            "LineVat" => 0.17,
                            "OriginalUnitPriceInclVat" => 1,
                            "OriginalUnitVat" => 0.17,
                            "OriginalLineTotalInclVat" => 1,
                            "OriginalLineVat" => 0.17,
                            "OriginalFeeFixed" => 0,
                            "BundleProductMerchantProductNo" => null,
                            "JurisCode" => null,
                            "JurisName" => null,
                            "VatRate" => 21,
                            "UnitPriceExclVat" => 0.83,
                            "LineTotalExclVat" => 0.83,
                            "OriginalUnitPriceExclVat" => 0.83,
                            "OriginalLineTotalExclVat" => 0.83,
                            "ExtraData" => [
                            ],
                            "ChannelProductNo" => "10443",
                            "MerchantProductNo" => "1",
                            "Quantity" => 1,
                            "CancellationRequestedQuantity" => 0,
                            "UnitPriceInclVat" => 1,
                            "FeeFixed" => 0,
                            "FeeRate" => 0,
                            "Condition" => "UNKNOWN",
                            "ExpectedDeliveryDate" => "2024-02-17T17:14:40.1464932+01:00"
                        ]
                    ],
                    "ShippingCostsInclVat" => 0,
                    "Phone" => "+31711000000",
                    "Email" => "test@channelengine.net",
                    "LanguageCode" => null,
                    "CompanyRegistrationNo" => null,
                    "VatNo" => null,
                    "PaymentMethod" => "iDEAL",
                    "PaymentReferenceNo" => null,
                    "CurrencyCode" => "EUR",
                    "OrderDate" => "2024-02-15T17:14:40.352181+01:00",
                    "ChannelCustomerNo" => null,
                    "ExtraData" => [
                        "VAT_CALCULATION_METHOD_KEY" => "FROM_PRICE_INCL"
                    ]
                ],
                [
                    "Id" => 966,
                    "ChannelName" => "AliExpress",
                    "ChannelId" => 5,
                    "GlobalChannelName" => "AliExpress",
                    "GlobalChannelId" => 1515,
                    "ChannelOrderSupport" => "ORDERS",
                    "ChannelOrderNo" => "CE-TEST-MFTF-2",
                    "CommercialOrderNo" => "CE-TEST-MFTF-2",
                    "MerchantOrderNo" => null,
                    "Status" => "NEW",
                    "IsBusinessOrder" => false,
                    "AcknowledgedDate" => null,
                    "CreatedAt" => "2024-02-12T09:38:54.1077977+01:00",
                    "UpdatedAt" => "2024-02-12T09:38:54.1077977+01:00",
                    "MerchantComment" => null,
                    "BillingAddress" => [
                        "Line1" => "Teststreet 22",
                        "Line2" => null,
                        "Line3" => null,
                        "Gender" => "NOT_APPLICABLE",
                        "CompanyName" => null,
                        "FirstName" => "T.",
                        "LastName" => "Tester",
                        "StreetName" => "Teststreet",
                        "HouseNr" => "22",
                        "HouseNrAddition" => null,
                        "ZipCode" => "1111 TT",
                        "City" => "Testtown",
                        "Region" => null,
                        "CountryIso" => "NL",
                        "Original" => "Teststreet 22"
                    ],
                    "ShippingAddress" => [
                        "Line1" => "Teststreet 22",
                        "Line2" => null,
                        "Line3" => null,
                        "Gender" => "NOT_APPLICABLE",
                        "CompanyName" => null,
                        "FirstName" => "T.",
                        "LastName" => "Tester",
                        "StreetName" => "Teststreet",
                        "HouseNr" => "22",
                        "HouseNrAddition" => null,
                        "ZipCode" => "1111 TT",
                        "City" => "Testtown",
                        "Region" => null,
                        "CountryIso" => "NL",
                        "Original" => "Teststreet 22"
                    ],
                    "SubTotalInclVat" => 123,
                    "SubTotalVat" => 21.35,
                    "ShippingCostsVat" => 0,
                    "TotalInclVat" => 123,
                    "TotalVat" => 21.35,
                    "OriginalSubTotalInclVat" => 123,
                    "OriginalSubTotalVat" => 21.35,
                    "OriginalShippingCostsInclVat" => 0,
                    "OriginalShippingCostsVat" => 0,
                    "OriginalTotalInclVat" => 123,
                    "OriginalTotalVat" => 21.35,
                    "SubTotalExclVat" => 101.65,
                    "TotalExclVat" => 101.65,
                    "ShippingCostsExclVat" => 0,
                    "OriginalSubTotalExclVat" => 101.65,
                    "OriginalShippingCostsExclVat" => 0,
                    "OriginalTotalExclVat" => 101.65,
                    "Lines" => [
                        [
                            "Id" => 1036,
                            "ChannelOrderLineNo" => null,
                            "Status" => "NEW",
                            "IsFulfillmentByMarketplace" => false,
                            "Gtin" => "2",
                            "Description" => "Testproduct",
                            "StockLocation" => [
                                "Id" => 1,
                                "Name" => "Logeecom 2"
                            ],
                            "UnitVat" => 21.35,
                            "LineTotalInclVat" => 123,
                            "LineVat" => 21.35,
                            "OriginalUnitPriceInclVat" => 123,
                            "OriginalUnitVat" => 21.35,
                            "OriginalLineTotalInclVat" => 123,
                            "OriginalLineVat" => 21.35,
                            "OriginalFeeFixed" => 0,
                            "BundleProductMerchantProductNo" => null,
                            "JurisCode" => null,
                            "JurisName" => null,
                            "VatRate" => 21,
                            "UnitPriceExclVat" => 101.65,
                            "LineTotalExclVat" => 101.65,
                            "OriginalUnitPriceExclVat" => 101.65,
                            "OriginalLineTotalExclVat" => 101.65,
                            "ExtraData" => [
                            ],
                            "ChannelProductNo" => "10320",
                            "MerchantProductNo" => "15",
                            "Quantity" => 1,
                            "CancellationRequestedQuantity" => 0,
                            "UnitPriceInclVat" => 123,
                            "FeeFixed" => 0,
                            "FeeRate" => 0,
                            "Condition" => "UNKNOWN",
                            "ExpectedDeliveryDate" => "2024-02-14T09:38:54.0235249+01:00"
                        ]
                    ],
                    "ShippingCostsInclVat" => 0,
                    "Phone" => "+31711000000",
                    "Email" => "test@channelengine.net",
                    "LanguageCode" => null,
                    "CompanyRegistrationNo" => null,
                    "VatNo" => null,
                    "PaymentMethod" => "iDEAL",
                    "PaymentReferenceNo" => null,
                    "CurrencyCode" => "EUR",
                    "OrderDate" => "2024-02-12T09:38:54.1067365+01:00",
                    "ChannelCustomerNo" => null,
                    "ExtraData" => [
                        "VAT_CALCULATION_METHOD_KEY" => "FROM_PRICE_INCL"
                    ]
                ]
            ],
            "Count" => $this->acknowledged ? 0 : 2,
            "TotalCount" => $this->acknowledged ? 0 : 2,
            "ItemsPerPage" => $this->acknowledged ? 0 : 2,
            "StatusCode" => 200,
            "RequestId" => null,
            "LogId" => null,
            "Success" => true,
            "Message" => null,
            "ValidationErrors" => [
            ]
        ];

        return OrdersPage::fromArray($response);
    }

    /**
     * @inheriDoc
     */
    public function acknowledge($orderId, $merchantOrderNo)
    {
        $this->acknowledged = true;

        return AcknowledgeResponse::fromArray(['Success' => true, 'StatusCode' => 200, 'Message' => null]);
    }

    /**
     * @inheirtDoc
     */
    public function getWithStatuses(DateTime $fromDate, $page, array $statuses = ['SHIPPED', 'CLOSED'])
    {
        $response = [
            "Content" => $page > 1 || ($this->acknowledged  && !$this->isOrderManualSyncCase()) ? [] : [
                [
                    "Id" => 970,
                    "ChannelName" => "AliExpress",
                    "ChannelId" => 5,
                    "GlobalChannelName" => "AliExpress",
                    "GlobalChannelId" => 1515,
                    "ChannelOrderSupport" => "ORDERS",
                    "ChannelOrderNo" => "CE-TEST-MFTF-" . ($this->isOrderManualSyncCase() ? '4' : '3'),
                    "CommercialOrderNo" => "CE-TEST-MFTF-" . ($this->isOrderManualSyncCase() ? '4' : '3'),
                    "MerchantOrderNo" => null,
                    "Status" => "SHIPPED",
                    "IsBusinessOrder" => false,
                    "AcknowledgedDate" => null,
                    "CreatedAt" => "2024-02-12T16:42:45.2549013+01:00",
                    "UpdatedAt" => "2024-02-12T16:42:54.7225092+01:00",
                    "MerchantComment" => null,
                    "BillingAddress" => [
                        "Line1" => "Teststreet 22",
                        "Line2" => null,
                        "Line3" => null,
                        "Gender" => "NOT_APPLICABLE",
                        "CompanyName" => null,
                        "FirstName" => "T.",
                        "LastName" => "Tester",
                        "StreetName" => "Teststreet",
                        "HouseNr" => "22",
                        "HouseNrAddition" => null,
                        "ZipCode" => "1111 TT",
                        "City" => "Testtown",
                        "Region" => null,
                        "CountryIso" => "NL",
                        "Original" => "Teststreet 22"
                    ],
                    "ShippingAddress" => [
                        "Line1" => "Teststreet 22",
                        "Line2" => null,
                        "Line3" => null,
                        "Gender" => "NOT_APPLICABLE",
                        "CompanyName" => null,
                        "FirstName" => "T.",
                        "LastName" => "Tester",
                        "StreetName" => "Teststreet",
                        "HouseNr" => "22",
                        "HouseNrAddition" => null,
                        "ZipCode" => "1111 TT",
                        "City" => "Testtown",
                        "Region" => null,
                        "CountryIso" => "NL",
                        "Original" => "Teststreet 22"
                    ],
                    "SubTotalInclVat" => 123,
                    "SubTotalVat" => 21.35,
                    "ShippingCostsVat" => 0,
                    "TotalInclVat" => 123,
                    "TotalVat" => 21.35,
                    "OriginalSubTotalInclVat" => 123,
                    "OriginalSubTotalVat" => 21.35,
                    "OriginalShippingCostsInclVat" => 0,
                    "OriginalShippingCostsVat" => 0,
                    "OriginalTotalInclVat" => 123,
                    "OriginalTotalVat" => 21.35,
                    "SubTotalExclVat" => 101.65,
                    "TotalExclVat" => 101.65,
                    "ShippingCostsExclVat" => 0,
                    "OriginalSubTotalExclVat" => 101.65,
                    "OriginalShippingCostsExclVat" => 0,
                    "OriginalTotalExclVat" => 101.65,
                    "Lines" => [
                        [
                            "Id" => 1040,
                            "ChannelOrderLineNo" => null,
                            "Status" => "SHIPPED",
                            "IsFulfillmentByMarketplace" => false,
                            "Gtin" => "2",
                            "Description" => "Testproduct",
                            "StockLocation" => [
                                "Id" => 1,
                                "Name" => "Logeecom 2"
                            ],
                            "UnitVat" => 21.35,
                            "LineTotalInclVat" => 123,
                            "LineVat" => 21.35,
                            "OriginalUnitPriceInclVat" => 123,
                            "OriginalUnitVat" => 21.35,
                            "OriginalLineTotalInclVat" => 123,
                            "OriginalLineVat" => 21.35,
                            "OriginalFeeFixed" => 0,
                            "BundleProductMerchantProductNo" => null,
                            "JurisCode" => null,
                            "JurisName" => null,
                            "VatRate" => 21,
                            "UnitPriceExclVat" => 101.65,
                            "LineTotalExclVat" => 101.65,
                            "OriginalUnitPriceExclVat" => 101.65,
                            "OriginalLineTotalExclVat" => 101.65,
                            "ExtraData" => [
                            ],
                            "ChannelProductNo" => "10950",
                            "MerchantProductNo" => "45",
                            "Quantity" => 1,
                            "CancellationRequestedQuantity" => 0,
                            "UnitPriceInclVat" => 123,
                            "FeeFixed" => 0,
                            "FeeRate" => 0,
                            "Condition" => "UNKNOWN",
                            "ExpectedDeliveryDate" => "2024-02-14T16:42:45.181646+01:00"
                        ]
                    ],
                    "ShippingCostsInclVat" => 0,
                    "Phone" => "+31711000000",
                    "Email" => "test@channelengine.net",
                    "LanguageCode" => null,
                    "CompanyRegistrationNo" => null,
                    "VatNo" => null,
                    "PaymentMethod" => "iDEAL",
                    "PaymentReferenceNo" => null,
                    "CurrencyCode" => "EUR",
                    "OrderDate" => "2024-02-12T16:42:45.2537767+01:00",
                    "ChannelCustomerNo" => null,
                    "ExtraData" => [
                        "VAT_CALCULATION_METHOD_KEY" => "FROM_PRICE_INCL"
                    ]
                ]
            ],
            "Count" => $page > 1 || ($this->acknowledged  && !$this->isOrderManualSyncCase()) ? 0 : 1,
            "TotalCount" => $page > 1 || ($this->acknowledged  && !$this->isOrderManualSyncCase()) ? 0 : 1,
            "ItemsPerPage" => 100,
            "StatusCode" => 200,
            "RequestId" => null,
            "LogId" => null,
            "Success" => true,
            "Message" => null,
            "ValidationErrors" => [
            ]
        ];

        return OrdersPage::fromArray($response);
    }

    /**
     * @return bool
     * @throws QueryFilterInvalidParamException
     */
    private function isOrderManualSyncCase()
    {
        /** @var ConfigurationManager $configManager */
        $configManager = ServiceRegister::getService(ConfigurationManager::CLASS_NAME);

        return (bool) $configManager->getConfigValue('ce_mftf_orders_manual_sync', false, false);
    }
}
