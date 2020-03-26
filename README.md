# EasyDaraz
This is a package created to make api endpoint calls to darz e-commerce site for sellers.

## How to initiate
1. Git Clone the repository
2. Update the Composer
3. Use these in your file

    * require_once(dirname(__FILE__) . '/vendor/autoload.php');
    * require_once(dirname(__FILE__) . '/src/Daraz.php');
    * use daraz\easydaraz\Daraz;

4. Define following variables
    * $apiKey = 'Your Darz API-Key';
    * $userId = 'Your Daraz E-mail';
    * $url = 'Your Daraz API URL'
        * Eg: https://api.sellercenter.daraz.lk';   - for Sri Lanka 