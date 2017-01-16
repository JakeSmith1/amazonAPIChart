# amazonAPIChart
search amazon for products, save them to mysql, view them in a table

###Features:
- Search Amazon's Product Advertising API for ASIN numbers
- Save product data to MySQL
- Error alert if a product cannot be looked up in Amazon's API
- Save button disabled after product information saved

###To run this application:
  * Get amazon product advertising api credentials in put in them in resources/config.php.
    * [amazon product advertising API](http://docs.aws.amazon.com/AWSECommerceService/latest/DG/Welcome.html)
  1. set up a Apache-MySQL-PHP environment such as MAMP, XAMPP.
  2. put the public_html and resources folders in the DocumentRoot, usually htdocs.
  3. start your MySQL server on port 3306.
  4. connect to MySQL from the command line (mysql -u root -p) and run:
    CREATE DATABASE products;
    USE products;
    CREATE TABLE items (ASIN CHAR(10), Name VARCHAR(220), MPN VARCHAR(60), Price VARCHAR(20));
  5. start your Apache server on a port and open your browser to http://localhost/:port/public_html/index.html
  6. Try entering these ASIN #s or find your own on amazon.
    B019U00D7K, B000BUB58K, B00TKFEEAS, B01CGGOZOM
