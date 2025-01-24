@props(['email', 'pageTitle'])

<header>
    @auth
        <section class="section-logout">
            <p>Logged in with {{ $email }}</p>
            <a class="btn-logout" href="{{ url('/logout') }}">Logout</a>
        </section>
    @endauth

    @guest
        <section class="section-login">
            <a class="btn-login" href="{{ url('/login') }}">Login</a>
        </section>
    @endguest
    <section class="section-border">
        <h1>{{ $pageTitle }}</h1>
    </section>
</header>
