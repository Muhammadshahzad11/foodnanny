@component('mail::message')
    # Subscriber Notification

    Hello,

    Subject : {{ $title }}
    {{ $message }}

    Thanks,
   <p> {{ config('app.name') }}</p>
@endcomponent
