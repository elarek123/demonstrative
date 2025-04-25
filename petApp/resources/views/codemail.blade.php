<html>
<head>
    <title>Welcome Email</title>
</head>
<body>
<h1>Welcome, {{ $username}}!</h1>
<p>Thank you for registering with our application.</p>

<p>Here your confirmation code is: <b>{{ $code }}</b></p>

<footer>
    &copy; {{ date('Y') }} {{ config('app.name') }}
</footer>
</body>
</html>
