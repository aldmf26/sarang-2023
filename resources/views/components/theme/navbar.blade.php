<header class="mb-5">
    @include('components.theme.header2')
    <nav class="main-navbar " style="background-color: #F7914D; ">
        <div class="container font-bold">
            <ul>
                <li class="menu-item">
                    <a href="{{ route('dashboard.index') }}"
                        class='menu-link {{ request()->route()->getName() == 'dashboard.index'? 'active_navbar_new': '' }}'>
                        <span>Dashboard</span>
                    </a>
                </li>
                @php
                    $navbar = DB::table('navbar')
                        ->orderBy('urutan', 'ASC')
                        ->get();
                @endphp
                @foreach ($navbar as $d)
                        @php
                            $string = $d->isi;
                            $string = str_replace(['[', ']', "'"], '', $string);
                            $array = explode(', ', $string);
                        @endphp
                        <li class="menu-item">
                            <a href="{{ route($d->route) }}"
                                class='menu-link 
                                    {{ in_array(request()->route()->getName(),$array)? ' active_navbar_new': '' }}'>
                                <span>{{ ucwords($d->nama) }}</span>
                            </a>
                        </li>
                @endforeach
            </ul>
        </div>
    </nav>

</header>
