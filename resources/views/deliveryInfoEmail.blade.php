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