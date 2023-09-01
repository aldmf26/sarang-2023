<div class="header-top">
    <div class="container">
        <div class="logo">
            <a href="dashboard">
                <center>
                    <img src="https://kerja-sarang-new.putrirembulan.com/uploads/buku_kerja.png" alt="Logo">
                </center>
            </a>
            <h5>KERJA SARANG</h5>
        </div>
        <div class="header-top-right">

            <div class="dropdown">
                <a href="#" id="topbarUserDropdown"
                    class="user-dropdown d-flex align-items-center dropend dropdown-toggle " data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <div class="avatar avatar-md2">
                        @php
                            if (empty(auth()->user()->posisi->id_posisi)) {
                                $idPosisi = 1;
                                $nama = 'No Name';
                                $posisi = '-';
                            } else {
                                $idPosisi = auth()->user()->posisi->id_posisi;
                                $nama = ucwords(auth()->user()->name);
                                $posisi = ucwords(auth()->user()->posisi->nm_posisi);
                            }
                            
                            $gambar = $idPosisi == 1 ? 'Admin' : 'Pengawas';
                        @endphp
                        <img src='{{ asset("img/$gambar.png") }}' alt="Avatar">
                    </div>
                    <div class="text">
                        <h6 class="user-dropdown-name">{{ $nama }}</h6>
                        <p class="user-dropdown-status text-sm text-muted">
                            {{ $posisi }}
                        </p>
                    </div>
                </a>
                @if (empty(auth()->user()->posisi->id_posisi))
                @else
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="topbarUserDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a>
                        </li>
                        <li>
                            <form id="myForm" method="post" action="{{ route('logout') }}">
                                @csrf
                            </form>
                            <a class="dropdown-item" href="#"
                                onclick="document.getElementById('myForm').submit();">Logout</a>
                        </li>
                    </ul>
                @endif
            </div>

            <!-- Burger button responsive -->
            <a href="#" class="burger-btn d-block d-xl-none">
                <i class="bi bi-justify fs-3"></i>
            </a>
        </div>
    </div>
</div>
