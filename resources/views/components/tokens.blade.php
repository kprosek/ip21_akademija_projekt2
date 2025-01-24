@props([
    'userFavouriteTokens',
    'dropdownList',
    'tokenFrom',
    'tokenTo',
    'isFromFavourite',
    'isToFavourite',
    'price' => null,
])

<section class="section-border">
    <section class="section-title">
        <i class="fa-solid fa-list"></i>
        <h2>Tokens</h2>
    </section>

    <form action="/show-price" method="get" class="form-select-tokens">
        @csrf
        <label id="dropdown_token_from">From</label>
        <section class="section-select-token">
            <button class="btn btn-favourite" name="btn-favourite" value="btn_from" type="submit" formmethod="post">
                @auth
                    <i class="{{ $isFromFavourite ? 'fa-solid' : 'fa-regular' }} fa-star icon-favourite fa-lg"></i>
                @endauth
            </button>
            <select name="dropdown_token_from" required>
                <option disabled selected value="">Select:</option>
                @foreach ($dropdownList as $item)
                    <option {{ old('dropdown_token_from', $tokenFrom) == $item ? 'selected' : '' }}>
                        {{ $item }}
                        @if (in_array($item, $userFavouriteTokens))
                            *
                        @endif
                    </option>
                @endforeach
            </select>
        </section>

        <label id="dropdown_token_to">To</label>
        <section class="section-select-token">
            <button class="btn btn-favourite" name="btn-favourite" value="btn_to" type="submit" formmethod="post">
                @auth
                    <i class="{{ $isToFavourite ? 'fa-solid' : 'fa-regular' }} fa-star icon-favourite fa-lg"></i>
                @endauth
            </button>
            <select name="dropdown_token_to" required>
                <option disabled selected value="">Select:</option>
                @foreach ($dropdownList as $item)
                    <option {{ old('dropdown_token_to', $tokenTo) == $item ? 'selected' : '' }}>
                        {{ $item }}
                        @if (in_array($item, $userFavouriteTokens))
                            *
                        @endif
                    </option>
                @endforeach
            </select>
        </section>

        <button class="btn btn-price" type="submit"><i class="fa-solid fa-magnifying-glass"></i>Show
            Price</button>
    </form>
</section>
