A Laravel Project demonstrating restaurant ordering system using GloriaFood API
    and delivery system using Doordash Drive classic API.
Changes since last commit: 
-Deleted 'getRequest' view as it was not necessary anymore for our application.
-Deleted 'gotData' view as it was not necessary anymore for our application.
-Deleted 'responseFromGF' view as it was not necessary anymore for our application.
-Deleted 'queryGF' route from 'web.php' file as it was used to run 'poll'
    method of 'PollController', and we took care of running this method on
    server using Laravel Task Scheduler.
-Deleted 'deliveryDetails' route from 'web.php' file as it was used to run
    'getUpdateOnDeliveries' method of 'DeliveryController', and we took care of
    running this method on server using Laravel Task Scheduler.
-Removed redundant commented code wherever seemed necessary.
-Added two new columns 'delivery_created_at' and 'last_updated_at' in 
        'deliveries' table. The former field will be filled while saving 
        the delivery details in the table from 'updated_at' key's value
        from the response of the 'create delivery' API call
        received from Doordash Drive Classic API, and the
        latter will be filled while updating the delivery details in the
        table from the same key's value from the respose of 'update delivery'
        API call received from Doordash Drive Classic API. 'saveDelivery'
        method of 'DelieveryController' will be used for these purposes.
