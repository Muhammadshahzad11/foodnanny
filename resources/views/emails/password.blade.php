<html>
<head>
    <title>Reset Password</title>
</head>
<body style="padding: 25px">
<p>Hello {{$info['name']}},</p>
<p>Your code is <b>{{$info['pin']}}</b></p>
<p>Please do not share your One Time Code With Anyone.
    You made a request to reset your password. Please
    discard if this wasn't you.</p>
<br>
<p style="margin-bottom: 0">Thanks,</p>
<p>{{ config('app.name') }}</p>
</body>
</html>
