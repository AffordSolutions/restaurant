A Laravel Project demonstrating restaurant ordering system using GloriaFood API
    and delivery system using Doordash Drive classic API.
Changes since last commit: 
-Made arrangement to run 'createDelivery' method of DeliveryController Controller
    from within 'PollController' Controller's 'poll()' method with the current Order Details.
    This should create a new delivery in the Doordash Developer portal under 
    'Delivery Simulator':
    https://developer.doordash.com/portal/integration/drive_classic/delivery_simulator
-Updated 'createDelivery' method of 'DeliveryController' to include an argument containing
    details of a new order.
    This method creates a fresh delivery on the Doordash Developer Portal under 
    'Delivery Simulator':
    https://developer.doordash.com/portal/integration/drive_classic/delivery_simulator
    with parameters obtained from the fresh order received over the
    Food Ordering API poll call.
-Added 'sample_order_with_delivery.md' file in the list of files to be ignored by git.
    As the name suggests, this file includes the details of an order placed with
    delivery details.
