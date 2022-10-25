# Project Restaurant
A Laravel Project demonstrating restaurant ordering system using GloriaFood API and delivery system using Doordash Drive classic API

---
#### Changes since last commit: 
1. Integrated Sendgrid Mail Send API: 
    1. The customer will receive a mail containing the delivery tracking URL assigned by the doordash drive classic API. This is done by creating a function called **sendDeliveryEmail** under 
    **DeliveryController** controller and then putting this function under **createDelivery** function.
1. Added a video demonstrating the project. This doesn't contain email integration.
    And it was created before integrating [Sendgrid Mail Send API](https://docs.sendgrid.com/for-developers/sending-email/quickstart-php).
1. Learned **Markdown** and used it to create a more meaningful README file.