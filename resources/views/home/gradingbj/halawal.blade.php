<x-theme.app title="{{ $title }}" table="T" sizeCard="12">
    <x-slot name="slot">
        <div class="row">
            @php
                $gudang = [
                    [
                        'name' => 'Siap Grade',
                        'url' => 'gradingbj.history_ambil',
                    ],
                    [
                        'name' => 'Gudang selesai grade',
                        'url' => 'gradingbj.gudang_bj',
                    ],
                ];

            @endphp
            @foreach ($gudang as $g)
                <div class="col-lg-3">
                    <a href="{{ route($g['url']) }}">
                        <div style="cursor:pointer;background-color: #8ca3f3" class="card border card-hover text-white">
                            <div class="card-front">
                                <div class="card-body">
                                    <h4 class="card-title text-white text-center"><img
                                            src="{{ asset('img/storage-stacks.png') }}" width="128"
                                            alt=""><br><br>
                                        {{ $g['name'] }}
                                    </h4>
                                </div>
                            </div>
                            <div class="card-back">
                                <div class="card-body">
                                    <h5 class="card-text text-white">{{ $g['name'] }}</h5>
                                    <p class="card-text">{{ $g['name'] }}</p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
    </x-slot>


</x-theme.app>
