<html>
<head>
    <title>Order Confirmation</title>
</head>
<body style="padding: 25px">
<p>Hello {{$name}},</p>
<p>Order ID : <b>{{$orderId}}</b></p>
<p>{{$message}}</p>
<br>
<p style="margin-bottom: 0">Thanks,</p>
<p>{{ config('app.name') }}</p>
</body>
</html>
