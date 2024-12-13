<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <div class="row">
            <div class="col-lg-3">
                <a href="{{route('hrga1.index')}}">
                    <div style="cursor:pointer;background-color: #8c8989" class="card border card-hover text-white">
                        <div class="card-front">
                            <div class="card-body">
                                <h4 class="card-title text-white text-center"><img src="{{ asset('img/notes.png') }}"
                                        width="128" alt=""><br><br>
                                    Pemohonan karyawan baru
                                </h4>
                            </div>
                        </div>
                        <div class="card-back">
                            <div class="card-body">
                                <h5 class="card-text text-white">Pemohonan Karyawan Baru</h5>
                                <p class="card-text">FRM.HRGA.01.01 - Permohonan Karyawan Baru</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3">
                <a href="{{ route('hasilwawancara.index') }}">
                    <div style="cursor:pointer;background-color: #8c8989" class="card border card-hover text-white">
                        <div class="card-front">
                            <div class="card-body">
                                <h4 class="card-title text-white text-center"><img src="{{ asset('img/notes.png') }}"
                                        width="128" alt=""><br><br>
                                    Hasil Wawancara
                                </h4>
                            </div>
                        </div>
                        <div class="card-back">
                            <div class="card-body">
                                <h5 class="card-text text-white">Hasil wawancara</h5>
                                <p class="card-text">FRM.HRGA.01.02 - Hasil Wawancara</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3">
                <a href="{{route('hrga3.index')}}">
                    <div style="cursor:pointer;background-color: #8c8989" class="card border card-hover text-white">
                        <div class="card-front">
                            <div class="card-body">
                                <h4 class="card-title text-white text-center"><img src="{{ asset('img/notes.png') }}"
                                        width="128" alt=""><br><br>
                                    Hasil evaluasi karyawan baru
                                </h4>
                            </div>
                        </div>
                        <div class="card-back">
                            <div class="card-body">
                                <h5 class="card-text text-white">Hasil evaluasi Karyawan Baru</h5>
                                <p class="card-text">FRM.HRGA.01.03 - Hasil Evaluasi Karyawan Baru</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-3">
                <a href="">
                    <div style="cursor:pointer;background-color: #8c8989" class="card border card-hover text-white">
                        <div class="card-front">
                            <div class="card-body">
                                <h4 class="card-title text-white text-center"><img src="{{ asset('img/notes.png') }}"
                                        width="128" alt=""><br><br>
                                    Data Pegawai
                                </h4>
                            </div>
                        </div>
                        <div class="card-back">
                            <div class="card-body">
                                <h5 class="card-text text-white">Hasil wawancara</h5>
                                <p class="card-text">FRM.HRGA.01.02 - Hasil Wawancara</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
    </x-slot>
</x-theme.app>
