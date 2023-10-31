<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <div class="row">
            @forelse ($data as $d)
                <div class="col-lg-3">
                    <a href="{{ route($d->route) }}">
                        <div style="cursor:pointer;background-color: #8ca3f3" class="card border card-hover text-white">
                            <div class="card-front">
                                <div class="card-body">
                                    <h4 class="card-title text-white text-center"><img
                                            src="{{ asset('img/' . $d->img) }}" width="128" alt=""><br><br>
                                        {{ ucwords($d->judul) }}
                                    </h4>
                                </div>
                            </div>
                            <div class="card-back">
                                <div class="card-body">
                                    <h5 class="card-text text-white">{{ ucwords($d->judul) }}</h5>
                                    <p class="card-text">{{ ucwords($d->deskripsi) }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <h6 class="text-warning"><em>Silahkan Kontak Presiden untuk buka akses !</em></h6>
            @endforelse
    </x-slot>
</x-theme.app>
