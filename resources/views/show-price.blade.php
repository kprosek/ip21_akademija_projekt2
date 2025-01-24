<x-layouts.layout :pageTitle="'Currency Tokens Calculated!'" :email="$email" :userFavouriteTokens="$userFavouriteTokens" :isFromFavourite="$isFromFavourite" :isToFavourite="$isToFavourite"
    :dropdownList="$dropdownList" :tokenFrom="$tokenFrom" :tokenTo="$tokenTo" :price="$price">

    @if (isset($price))
        <section class="section-border-price">
            <p class="text-token-price">Current Token value: 1 {{ $tokenFrom }} = {{ $price }}
                {{ $tokenTo }}
            </p>
        </section>
    @endif
</x-layouts.layout>
