<footer>
    <div id="footer">
        <div class="container-fluid">
            <div class="row">
                <div class="col-6 col-sm-6">
                    <div class="headline">Account</div>
                    <ul>
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                        @else
                            <li><a href="{{ route('logout') }}">Logout</a></li>
                        @endguest
                    </ul>
                </div>

                <div class="col-6 col-sm-6 text-end">
                    <span class="text">
                        Licenced to {{ config('app.name') }}
                        <br>
                        &copy; {{ date('Y') }} <a href="http://www.elegantmedia.com.au" target="_blank">Elegant Media</a>
                    </span>
                    <br>
                    <span class="text">{{-- Made with Love --}}</span>
                </div>
            </div>
        </div>
    </div>
</footer>
