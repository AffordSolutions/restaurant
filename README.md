A Laravel Project demonstrating restaurant ordering system using GloriaFood API.
Changes since last commit: 
-Added a schedule in Kernel.php to poll for orders every minute. This will work only if the project is run locally.
- A model named "order" is created and connected with the database table "orders123" so that new orders can be saved in it.
Every new order is saved to the table successfully, provided that there is only one new order available for every api call made among the three restaurants.