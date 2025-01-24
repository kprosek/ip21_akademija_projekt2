@props(['userFavouriteTokens'])

<section class="section-border">
    <section class="section-title">
        <i class="fa-regular fa-star"></i>
        <h2>Favourites</h2>
    </section>

    <section class="list-favourites">
        <ul>
            @foreach ($userFavouriteTokens as $token)
                <li>{{ $token }}</li>
            @endforeach
        </ul>
    </section>
</section>
