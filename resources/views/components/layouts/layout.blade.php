<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Crypto Currencies - Login</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <x-header :email="$email" :pageTitle="$pageTitle" />

    <main>
        @auth
            <x-favourites :userFavouriteTokens="$userFavouriteTokens" />
        @endauth

        <x-tokens :userFavouriteTokens="$userFavouriteTokens" :dropdownList="$dropdownList" :tokenFrom="$tokenFrom" :tokenTo="$tokenTo" :isFromFavourite="$isFromFavourite"
            :isToFavourite="$isToFavourite" />

        {{ $slot }}
    </main>

    <x-footer />
</body>

</html>
