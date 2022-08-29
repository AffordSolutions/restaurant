A Laravel Project demonstrating restaurant ordering system using GloriaFood API
    and delivery system using Doordash Drive classic API.
Changes since last commit: 
-Created a table named 'deliveries' to register 'id', 'status', 'dasher_status',
    and 'delivery_tracking_url' for every new delivery.
    This table will be updated whenever an update is made to the delivery i.e.
    if it is cancelled or if some progress is made towards the delivery.
-Created 'saveDelivery' method and incorporated it in the 'createDelivery' method
    under 'DelvieryController' to save relevant information from the response of
    the API call made to Doordash Drive classic API for creating delivery in
    'deliveries' table. This method is used to update/delete the entries, too.
-Created a DELETE SQL statement to delete the entries which contain the
    details of a 'delivered' delivery from the 'deliveries' database as those 
    details are not necessary for our application to store. This functionality 
    is added under 'saveDelivery' method of 'DeliveryController' 
    Controller.
-Created a 'getUpdateOnDeliveries' method under 'DelvieryController' to 
    get an update on already registered deliveries. Incorporated
    'saveDelivery' method in this method to update/delete a particular record
    according to the update received from the API call made to
    Doordash Drive classic API .
-Creted a local task scheduler to run 'getUpdateOnDeliveries' method of
    'DeliveryController' Controller automatically every minute after 
    'poll' method of 'PollController' Controller is run by the same scheduler.
<!-- In a future commit:
-Created a route to take the user to the delivery tracking url once the order is
placed right from the page used to place the order.
-->
