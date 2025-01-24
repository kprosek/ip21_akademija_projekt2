<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Crypto Currencies - Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <header>
        <h1>Welcome to Login page!</h1>
    </header>

    <main>
        <form action="/login" method="post" class="form-login">
            @csrf
            <label id="email">Username:</label>
            <input name="email" placeholder="jane.doe@email.com" value="{{ old('email') }}">

            @error('email')
                <p class="text-error">{{ $message }}</p>
            @enderror

            <label id="password">Password:</label>
            <input name="password" type="password" placeholder="********">

            @error('password')
                <p class="text-error">{{ $message }}</p>
            @enderror
            <button class="btn">Login</button>
        </form>

        @error('error')
            <p class="text-error">
                {{ $message }}
            </p>
        @enderror
        <a class="btn-back" href="{{ url('/') }}">Go Back</a>
    </main>

    <footer>
        <p class="text-footer">Project created in 2025</p>
    </footer>
</body>

</html>
