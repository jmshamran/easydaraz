<?php
/*
 * (c) Shamran <jmshamran22@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace shamran\easydaraz;

use Spatie\ArrayToXml\ArrayToXml;
use GuzzleHttp\Client;

class Daraz
{
    const VERSION = '1.0';
    const FORMAT = 'JSON';
    private $defaultParams, $apiKey, $url;

    public function __construct($UserID, $apiKey, $url)
    {
        $this->setApiKeyAttribute($apiKey);
        $this->setDefaultParamAttributes($UserID);
        $this->setDefaultUrl($url);
    }

    /**
     * Use this call to retrieve the list of all product categories in the system.
     * @return array
     */
    public function getCategoryTree()
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetCategoryTree',
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to get a list of attributes with options for a given category.
     * It will also display attributes for , with their possible values listed TaxClass as options.
     * @param $category_ID integer
     * @return array
     */
    public function getCategoryAttributes($category_ID)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetCategoryAttributes',
            'PrimaryCategory' => $category_ID,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to upload a single image file and accept binary stream with file content.
     * Allowed image formats are JPG and PNG. The maximum size of an image file is 1MB.
     * @param $file_path string
     * @return array
     */
    public function uploadImage($file_path)
    {

        $parameters = $this->setSpecificParam([
            'Action' => 'UploadImage',
            'Timestamp' => $this->getTimeStamp(),
        ]);
        $image = file_get_contents($file_path);
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString, $image);
    }

    /**
     * Use this call to migrate a single image from an external site to Daraz site.
     * Allowed image formats are JPG and PNG. The maximum size of an image file is 1MB.
     * @param $image_url string
     * @return array
     */
    public function migrateImage($image_url)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'MigrateImage',
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $img_array = [
            'Image' => [
                'Url' => $image_url
            ]
        ];

        $image_xml = ArrayToXml::convert($img_array, 'Request');
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString, $image_xml);
    }

    /**
     * Use this call to migrate multiple images from an external site to Daraz site.
     * Allowed image formats are JPG and PNG. The maximum size of an image file is 1MB.
     * @param $image_url_1 string
     * @return array
     */
    public function migrateImages($image_url_1, $image_url_2 = Null, $image_url_3 = Null, $image_url_4 = Null, $image_url_5 = Null, $image_url_6 = Null, $image_url_7 = Null, $image_url_8 = Null)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'MigrateImages',
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $img_array = [
            'Images' => [
                'Url' => $image_url_1,
                'Url' => $image_url_2,
                'Url' => $image_url_3,
                'Url' => $image_url_4,
                'Url' => $image_url_5,
                'Url' => $image_url_6,
                'Url' => $image_url_7,
                'Url' => $image_url_8,
            ]
        ];

        $image_xml = ArrayToXml::convert($img_array, 'Request');
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString, $image_xml);
    }

    /**
     * Use this call to get the returned information from the system for the UploadImages and MigrateImages API.
     * @param $request_ID mixed
     * @return array
     */
    public function getResponse($request_ID)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetResponse',
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $id = [
            'RequestId' => [$request_ID]
        ];

        $response_xml = ArrayToXml::convert($id, 'Request');
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString, $response_xml);
    }

    /**
     * Use this call to retrieve all product brands in the system.
     * @param $limit integer
     * @param $offset integer
     * @return array
     */
    public function getBrands($limit, $offset)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetBrands',
            'Timestamp' => $this->getTimeStamp(),
            'Limit' => $limit,
            'Offset' => $offset
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to create a new product.
     * One item may contain at lest one SKU which has 8 images.
     * This API does not support creating multiple products in one request.
     * @param $product_array array
     * @return array
     * @throws Exception
     */
    public function createProduct($product_array)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'CreateProduct',
            'Timestamp' => $this->getTimeStamp(),
        ]);
        if (!is_array($product_array)) throw new \Exception('Invalid Parameter: Array Expected');

        $product_xml = ArrayToXml::convert($product_array, 'Request');
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString, $product_xml);
    }

    /**
     * Use this call to set the images for an existing product by associating one or more image URLs with it.
     * System supports a maximum of 100 SellerSkus in one request.
     * The first image passed in becomes the default image of the product.
     * There is a hard limit of at most 8 images per SKU. You can also use the call to set images
     * @param $image_array array
     * @return array
     * @throws Exception
     */
    public function setImages($image_array)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'SetImages',
            'Timestamp' => $this->getTimeStamp(),
        ]);
        if (!is_array($image_array)) throw new \Exception('Invalid Parameter: Array Expected');

        $image_xml = ArrayToXml::convert($image_array, 'Request');
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString, $image_xml);
    }

    /**
     * Use this call to get all products.
     * @return array
     */
    public function getAllProducts()
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetProducts',
            'Timestamp' => $this->getTimeStamp(),
            'Filter' => 'all'
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to get all or a range of products.
     * @param null $filter string
     * @param null $created_after datetime
     * @param null $created_before datetime
     * @param null $updated_after datetime
     * @param null $updated_before datetime
     * @param null $search string
     * @param null $limit integer
     * @param null $options integer
     * @param null $offset integer
     * @param null $sku_seller_list array of strings
     * @return array
     */
    public function getProducts($filter = null, $created_after = null, $created_before = null, $updated_after = null, $updated_before = null, $search = null, $limit = null, $options = null, $offset = null, $sku_seller_list = null)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetProducts',
            'Timestamp' => $this->getTimeStamp(),
            'Filter' => $filter,
            'CreatedAfter' => $created_after,
            'CreatedBefore' => $created_before,
            'UpdatedAfter' => $updated_after,
            'UpdatedBefore' => $updated_before,
            'Search' => $search,
            'Limit' => $limit,
            'Options' => $options,
            'Offset' => $offset,
            'SkuSellerList' => $sku_seller_list,
        ]);
        $this->removeEmptyFields($parameters);
        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to update attributes or SKUs of an existing product.
     * Note that one request can update only 1 product.
     * @param $update_product_array array
     * @return array
     * @throws Exception
     */
    public function updateProduct($update_product_array)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'UpdateProduct',
            'Timestamp' => $this->getTimeStamp(),
        ]);
        if (!is_array($update_product_array)) throw new \Exception('Invalid Parameter: Array Expected');

        $update_product_xml = ArrayToXml::convert($update_product_array, 'Request');
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString, $update_product_xml);
    }

    /**
     * Use this call to update the price and quantity of one or more existing products.
     * The maximum number of products that can be updated is 50, but20 is recommended.
     * @param $update_price_quantity_array array
     * @return array
     * @throws Exception
     */
    public function updatePriceQuantity($update_price_quantity_array)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'UpdatePriceQuantity',
            'Timestamp' => $this->getTimeStamp(),
        ]);
        if (!is_array($update_price_quantity_array)) throw new \Exception('Invalid Parameter: Array Expected');

        $update_price_quantity = ArrayToXml::convert($update_price_quantity_array, 'Request');
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString, $update_price_quantity);
    }

    /**
     * Use this call to get the order details for a single order.
     * It is different from , which gets the customer details of multiple orders.
     * @param $order_ID long
     * @return array
     */
    public function getOrder($order_ID)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetOrder',
            'OrderId' => $order_ID,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**Use this call to get the customer details for a range of orders.
     * @param null $created_after datetime
     * @param null $created_before datetime
     * @param null $updated_after datetime
     * @param null $updated_before datetime
     * @param null $limit integer
     * @param null $offset integer
     * @param null $status string
     * @param null $sort_by string
     * @param null $sort_direction string
     * @return array
     */
    public function getOrders($status = null, $created_after = null, $created_before = null, $updated_after = null, $updated_before = null, $limit = null, $offset = null, $sort_by = null, $sort_direction = null)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetOrders',
            'Status' => $status,
            'CreatedAfter' => $created_after,
            'CreatedBefore' => $created_before,
            'UpdatedAfter' => $updated_after,
            'UpdatedBefore' => $updated_before,
            'Limit' => $limit,
            'Offset' => $offset,
            'SortBy' => $sort_by,
            'SortDirection' => $sort_direction,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $this->removeEmptyFields($parameters);
        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to get the item information of one or more orders.
     * @param $order_ID long
     * @return array
     */
    public function getOrderItems($order_ID)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetOrderItems',
            'OrderId' => $order_ID,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to get the item information of one or more orders.
     * @param $order_list_array array of long
     * @return array
     */
    public function getMultipleOrderItems($order_list_array)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetMultipleOrderItems',
            'OrderIdList' => $order_list_array,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to set the invoice access key.
     * @param $order_item_ID long
     * @param $invoice_number string
     * @return array
     */
    public function setInvoiceNumber($order_item_ID, $invoice_number)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'SetInvoiceNumber',
            'OrderItemId' => $order_item_ID,
            'InvoiceNumber' => $invoice_number,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString);
    }

    /**
     * Use this call to mark order items as being packed.
     * @param $order_item_IDs array of long
     * @param $delivery_type string
     * @param $shipping_provider string
     * @return array
     */
    public function setStatusToPackedByMarketplace($order_item_IDs, $delivery_type, $shipping_provider = Null)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'SetStatusToPackedByMarketplace',
            'OrderItemIds' => $order_item_IDs,
            'DeliveryType' => $delivery_type,
            'ShippingProvider' => $shipping_provider,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $this->removeEmptyFields($parameters);
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString);
    }

    /**
     * Use this call to mark an order item as being ready to ship.
     * @param $order_item_IDs array of long
     * @param $delivery_type string
     * @param $shipping_provider string
     * @param $tracking_number string
     * @param $serial_number mixed
     * @return array
     */
    public function setStatusToReadyToShip($order_item_IDs, $delivery_type, $tracking_number, $shipping_provider = Null, $serial_number = null)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'SetStatusToReadyToShip',
            'OrderItemIds' => $order_item_IDs,
            'DeliveryType' => $delivery_type,
            'TrackingNumber' => $tracking_number,
            'ShippingProvider' => $shipping_provider,
            'SerialNumber' => $serial_number,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $this->removeEmptyFields($parameters);
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString);
    }

    /**
     * Use this call to retrieve order-related documents, including invoices, shipping labels, and shipping parcels.
     * @param $order_item_IDs array of long
     * @param $document_type string
     * @return array
     */
    public function getDocument($order_item_IDs, $document_type)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetDocument',
            'OrderItemIds' => $order_item_IDs,
            'DocumentType' => $document_type,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to get additional error context for SetStatusToCanceled.
     * @return array
     */
    public function getFailureReasons()
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetFailureReasons',
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to cancel a single item.
     * @param $order_item_ID long
     * @param $reason_ID long
     * @param $reason_detail string
     * @return array
     */
    public function setStatusToCanceled($order_item_ID, $reason_ID, $reason_detail = null)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'SetStatusToCanceled',
            'OrderItemId' => $order_item_ID,
            'ReasonId' => $reason_ID,
            'ReasonDetail' => $reason_detail,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $this->removeEmptyFields($parameters);
        $queryString = $this->getQueryString($parameters);
        return $this->sendPostRequest($queryString);
    }

    /**
     * Use this call to get the quality control status of items being listed.
     * @param $limit integer
     * @param $offset integer
     * @param $sku_seller_list array of strings
     * @return array
     */
    public function getQCStatus($limit = null, $offset = null, $sku_seller_list = null)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetQcStatus',
            'Limit' => $limit,
            'Offset' => $offset,
            'SkuSellerList' => $sku_seller_list,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $this->removeEmptyFields($parameters);
        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to get the payout status for a specified period.
     * @param $created_after integer
     * @return array
     */
    public function getPayoutStatus($created_after)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetPayoutStatus',
            'CreatedAfter' => $created_after,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to get transaction or fee details for a specified period.
     * @param $start_time date (YYYY-MM-DD)
     * @param $end_time date (YYYY-MM-DD)
     * @param $max_item integer
     * @param $trans_type integer
     * @param $limit integer
     * @param $offset integer
     * @param $skusellerlist array of strings
     * @return array
     */
    public function getTransactionDetails($start_time = null, $end_time = null, $max_item = null, $trans_type = null, $limit = null, $offset = null, $skusellerlist = null)
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetTransactionDetails',
            'startTime' => $start_time,
            'endTime' => $end_time,
            'maxItem' => $max_item,
            'transType' => $trans_type,
            'Limit' => $limit,
            'Offset' => $offset,
            'SkuSellerList' => $skusellerlist,
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $this->removeEmptyFields($parameters);
        $queryString = $this->getQueryString($parameters);
        return $this->getRequest($queryString);
    }

    /**
     * Use this call to get seller information by the current user ID.
     * @return array
     */
    public function getSeller()
    {
        $parameters = $this->setSpecificParam([
            'Action' => 'GetSeller',
            'Timestamp' => $this->getTimeStamp(),
        ]);

        $queryString = $this->getQueryString($parameters);

        return $this->getRequest($queryString);
    }

    /** GET Request cURL
     * @param $queryString string
     * @return array
     */
    public function getRequest($queryString)
    {

        $url = $this->url . '?' . $queryString;
        $client = new Client();
        $response = $client->request('GET', $url);


        return [
            'status' => $response->getReasonPhrase(),
            'http_code' => $response->getStatusCode(),
            'response' => json_decode($response->getBody()->getContents(), true),
        ];
    }

    /**
     * POST Request cURL
     * @param $queryString string
     * @param $post mixed
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function sendPostRequest($queryString, $post = null)
    {
        $url = $this->url . '?' . $queryString;
        $client = new Client();
        $response = $client->request('POST', $url, ['body' => $post]);

        return [
            'status' => $response->getReasonPhrase(),
            'http_code' => $response->getStatusCode(),
            'response' => json_decode($response->getBody()->getContents(), true),
        ];
    }

    private function setApiKeyAttribute($apiKey)
    {
        if (empty($apiKey)) throw new \Exception('No API Key Found');

        $this->apiKey = $apiKey;
    }

    private function setDefaultParamAttributes($UserID)
    {
        if (empty($UserID)) throw new \Exception('No User ID Found');

        $this->defaultParams = array(
            'UserID' => $UserID,
            'Version' => self::VERSION,
            'Format' => self::FORMAT,
        );
    }

    private function setDefaultUrl($url)
    {
        if (empty($url)) throw new \Exception('No URL Found');
        $this->url = $url;
    }

    private function setSpecificParam($array)
    {
        $parameters = $this->defaultParams;
        foreach ($array as $key => $value)
            $parameters[$key] = $value;
        return $parameters;
    }

    /**
     * This function gets current datetime, to be mostly used in parameter timestamps
     * date_default_timezone_set is only needed if timezone in php.ini is not set correctly.
     * @return DateTime|string
     * @throws \Exception
     */
    private function getTimeStamp()
    {
        date_default_timezone_set("UTC");
        $now = new \DateTime();
        $now = $now->format(\DateTime::ISO8601);
        return $now;
    }

    private function removeEmptyFields(&$array)
    {
        foreach ($array as $key => $value) {
            if (empty($value)) unset($array[$key]);
        }
    }

    private function getQueryString($parameters)
    {
        ksort($parameters);
        $encoded = array();
        foreach ($parameters as $name => $value) {
            $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
        }
        $concatenated = implode('&', $encoded);
        $api_key = $this->apiKey;
        $parameters['Signature'] =
            rawurlencode(hash_hmac('sha256', $concatenated, $api_key, false));
        return http_build_query(
            $parameters,
            '',
            '&',
            PHP_QUERY_RFC3986
        );
    }
}
