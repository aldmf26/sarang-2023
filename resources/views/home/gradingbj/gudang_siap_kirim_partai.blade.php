<x-theme.app title="{{ $title }}" table="Y" sizeCard="8">
    <x-slot name="cardHeader">
        @include('home.gradingbj.nav')
    </x-slot>

    <x-slot name="cardBody">
        <section x-data="{ cek: [], ttlPcs: 0, ttlGr: 0 }">
            <div class="row">
                <div class="col-lg-4">
                    <input type="text" id="tbl1input" class="form-control form-control-sm mb-2" placeholder="cari">
                </div>
                
                <div class="col-lg-8">
                    <form action="{{ route('pengiriman.kirim') }}" method="post">
                        @csrf
                        <a href="{{route('pengiriman.gudang')}}" class="btn btn-sm btn-info"
                            href=""><i class="fa fa-warehouse"></i> Gudang</a>
                        <a href="{{route('packinglist.pengiriman')}}" class="btn btn-sm btn-primary"
                            href=""><i class="fa fa-clipboard-list"></i> Packinglist</a>
                    
                        <input type="hidden" name="no_box" class="form-control" :value="cek.join(',')">
                        <button x-transition x-show="cek.length"
                            class="btn btn-sm btn-primary" type="submit">
                            <i class="fas fa-plus"></i>
                            Kirim
                            <span class="badge bg-info" x-text="cek.length" x-transition></span>
                            <span  x-transition><span x-text="ttlPcs"></span> Pcs <span x-text="ttlGr"></span> Gr</span>
                        </button>
                    </form>
                </div>
                <div class="scrollable-table col-lg-12">
                    <table id="tbl1" class="table table-hover table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="dhead">No Box Grading</th>
                                <th class="dhead text-center">Grade</th>
                                <th class="dhead text-end">Pcs</th>
                                <th class="dhead text-end">Gr</th>
                                <th class="dhead text-center">Aksi</th>
                            </tr>
    
                        </thead>
    
                        @php
                            $ttlPcs = 0;
                            $ttlGr = 0;
                            foreach ($gudang as $d) {
                                if($d->pcs - $d->pcs_pengiriman >= 0 && $d->gr - $d->gr_pengiriman >= 0){
                                    $ttlPcs += $d->pcs - $d->pcs_pengiriman;
                                    $ttlGr += $d->gr - $d->gr_pengiriman;
                                }
                            }
                        @endphp
                        <tr>
                            <td class=" dheadstock h6">Total</td>
                            <td class="dheadstock"></td>
                            <td class="text-end dheadstock h6 ">{{ $ttlPcs }}</td>
                            <td class="text-end dheadstock h6 ">{{ $ttlGr }}</td>
                            <td class="dheadstock">
                                {{-- <div x-show="cek.length">
                                    Dipilih <br> Pcs : <span></span> Gr : <span></span>
                                </div> --}}
                            </td>
                        </tr>
                        <tbody>
                            @foreach ($gudang as $d)
                                @if ($d->pcs - $d->pcs_pengiriman > 0 && $d->gr - $d->gr_pengiriman > 0)
                                    <tr @click="
                                            if (cek.includes('{{ $d->no_box }}')) {
                                                cek = cek.filter(x => x !== '{{ $d->no_box }}'); 
                                                ttlPcs -= {{ $d->pcs - $d->pcs_pengiriman }}; 
                                                ttlGr -= {{ $d->gr - $d->gr_pengiriman }};
                                            } else {
                                                cek.push('{{ $d->no_box }}'); 
                                                ttlPcs += {{ $d->pcs - $d->pcs_pengiriman }}; 
                                                ttlGr += {{ $d->gr - $d->gr_pengiriman }};
                                            }
                                        ">
                                        <td>P{{ $d->no_box }}</td>
                                        <td class="text-primary text-center pointer">   
                                            <span class="detail" data-nobox="{{ $d->no_box }}">{{ $d->grade }}</span>  
                                        </td>
                                        <td class="text-end">{{ $d->pcs - $d->pcs_pengiriman }}</td>
                                        <td class="text-end">{{ $d->gr - $d->gr_pengiriman }}</td>
                                        <td align="right" class="d-flex justify-content-evenly">
                                            <input type="checkbox" class="form-check"
                                                :checked="cek.includes('{{ $d->no_box }}')" name="id[]" id=""
                                                value="{{ $d->no_box }}">
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        
        <x-theme.modal title="Detail" idModal="detail" btnSave="T">
            <div class="loading d-none">
                <x-theme.loading />
            </div>
            <div id="load_detail"></div>
        </x-theme.modal>

        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')

                $('.detail').click(function(e) {
                    e.preventDefault();
                    const no_box = $(this).data('nobox')
                    $("#detail").modal('show')
                    $.ajax({
                        type: "GET",
                        url: "{{ route('gradingbj.detail') }}",
                        data: {
                            no_box,
                        },
                        beforeSend: function() {
                            $("#load_detail").html("");
                            $('.loading').removeClass('d-none');
                        },
                        success: function(r) {
                            $('.loading').addClass('d-none');
                            $("#load_detail").html(r);
                            loadTable('tblDetail')
                        }
                    });
                });
            </script>
        @endsection
    </x-slot>
</x-theme.app>

