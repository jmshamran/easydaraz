## EasyDaraz
***This is a package created to make api endpoint calls to Daraz e-commerce site for sellers.***

### How to initiate?
1. Git Clone the repository
2. Update the Composer
3. Use these in your file  
   ```go
    require_once(dirname(__FILE__) . '/vendor/autoload.php');
    require_once(dirname(__FILE__) . '/src/Daraz.php');
    use daraz\easydaraz\Daraz;
   ```
4. Define following variables in your code.
    ```go
    $apiKey = 'Your Darz API-Key';
    $userId = 'Your Daraz E-mail';
    $url = 'Your Daraz API URL';
   ```
    > ###### ex: https://api.sellercenter.daraz.lk   - for Sri Lanka
5. Instantiate the class
    * `$daraz = new Daraz($userId, $apiKey, $url);`

### Available API Endpoints

***Use these calls to get required results. Get complete information on Daraz API [here.](https://www.daraz.com/sellerapi-docs)***
    
0. ``getSeller()`` - To get seller information by the current user ID.

1. ``getCategoryTree()`` - Retrieve the list of all product categories in the system.

2. ``getCategoryAttributes()`` -  Get a list of attributes with options for a given category.

3. ``getBrands()`` - Retrieve all product brands in the system.

4. ``createProduct()`` - Create a product (use an array of attributes).

5. ``updateProduct()`` - Update attributes or SKUs of an existing product. One request can update only 1 product.

6. ``uploadImage()`` - To upload a single image file and accept binary stream with file content.

7. ``migrateImage()`` - To migrate a single image from an external site to Daraz site (Response provides migrated Image URLs).

8. ``migrateImages()`` -  To migrate multiple images from an external site to Daraz site (This call's response doesn't provide migrated image URLs).

9. ``getResponse()`` - To get the returned information from the system for the UploadImages and MigrateImages API

10. ``getAllProducts()`` - Get all or a range of products.

11. ``getProducts()`` - To get all or a range of products.

12. ``setImages()`` - To set the images for an existing product by associating one or more image URLs with it.

13. ``updatePriceQuantity()`` - To update the price and quantity of one or more existing products.

14. ``getOrder()`` - To get the order details for a single order.

15. ``getOrders()`` - To get the customer details for a range of orders.

16. ``getOrderItems()`` - To get the item information of one or more orders.

17. ``getMultipleOrderItems()`` - To get the item information of one or more orders.

18. ``setInvoiceNumber()`` - To set the invoice access key.

19. ``setStatusToPackedByMarketplace()`` - To mark order items as being packed.

20. ``setStatusToReadyToShip()`` - To mark an order item as being ready to ship.

21. ``getDocument()`` - To retrieve order-related documents, including invoices, shipping labels, and shipping parcels.

22. ``getFailureReasons()`` - To get additional error context for SetStatusToCanceled.

23. ``setStatusToCanceled()`` - To cancel a single item.

24. ``getQCStatus()`` - To get the quality control status of items being listed.

25. ``getPayoutStatus()`` - To get the payout status for a specified period.

26. ``getTransactionDetails()`` - To get transaction or fee details for a specified period.