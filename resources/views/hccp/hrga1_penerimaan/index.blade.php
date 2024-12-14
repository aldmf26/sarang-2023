<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <div class="row">
            @php
                $datas = [
                    [
                        'param' => 'hrga1',
                        'title' => 'Pemohonan Karyawan Baru',
                        'deskripsi' => 'FRM.HRGA.01.01 - Permohonan Karyawan Baru',
                    ],
                    [
                        'param' => 'hrga2',
                        'title' => 'Hasil Wawancara',
                        'deskripsi' => 'FRM.HRGA.01.02 - Hasil Wawancara',
                    ],
                    [
                        'param' => 'hrga3',
                        'title' => 'Hasil Evaluasi Karyawan Baru',
                        'deskripsi' => 'FRM.HRGA.01.03 - Hasil Evaluasi Karyawan Baru',
                    ],
                    [
                        'param' => 'hrga4',
                        'title' => 'Data Pegawai',
                        'deskripsi' => 'FRM.HRGA.01.04 - Data Pegawai',
                    ],
                ];
            @endphp
            @foreach ($datas as $d => $i)
                <div class="col-lg-3">
                    <a href="{{ route('divisi.index', $i['param']) }}">
                        <div style="cursor:pointer;background-color: #8c8989" class="card border card-hover text-white">
                            <div class="card-front">
                                <div class="card-body">
                                    <h4 class="card-title text-white text-center"><img
                                            src="{{ asset('img/notes.png') }}" width="128" alt=""><br><br>
                                        {{ ucfirst($i['title']) }}
                                    </h4>
                                </div>
                            </div>
                            <div class="card-back">
                                <div class="card-body">
                                    <h5 class="card-text text-white"> {{ ucfirst($i['title']) }}
                                    </h5>
                                    <p class="card-text"> {{ ucfirst($i['deskripsi']) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach

    </x-slot>
</x-theme.app>
