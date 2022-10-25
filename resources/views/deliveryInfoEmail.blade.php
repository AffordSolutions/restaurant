{{-- This file can be safely deleted, as it was a part of the Mailgun SMTP integration
    and now we have integrated Sendgrid Mail Send API, but let's keep it as it is.
    There is no harm.
--}}
@component('mail::message')
#Delivery details for your order

Hello, {{$name}}, Please click on the below button to track the delivery of your food.
{{$url}}
@component('mail::button',['url'=> $url])
Track Order
@endcomponent

Thanks,<br>
{{config('app.name')}}
@endcomponent