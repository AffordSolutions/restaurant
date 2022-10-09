A Laravel Project demonstrating restaurant ordering system using GloriaFood API
    and delivery system using Doordash Drive classic API.
Changes since last commit: 
-Added "changes_before_commit.md" and "Mailgun_integration.md" in .gitignore.
    The latter contains command line steps to install mailgun on a local machine
    and send a test mail to an email of the user's choice. This has been included in
    .gitignore since it contains my mailgun credentials.
-Provision to send an email with delivery tracking URL to customers placing orders
    has been added in "createDelivery" method of "DeliveryController" controller.
-Created a Mailable named "DeliveryCreated" using artisan command "php artisan make:mail".
    Created a "build" method in it passing "deliveryInfoEmail" view to it, which is the 
    view of the email to be sent, with parameters exclusive to the delivery to notify
    which the email is sent.
-Changed the default mailer ot "mailgun" in "mail.php" located under "config" directory.

    
