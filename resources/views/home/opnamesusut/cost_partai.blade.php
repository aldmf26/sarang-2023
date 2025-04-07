<x-theme.app title="{{ $title }}" table="Y" sizeCard="11" cont="container-fluid">
    <x-slot name="cardHeader">
        <h6 class="float-start mt-1">{{ $title }}</h6>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            @include('home.cocokan.nav')

            <div class="col-lg-12">
                <form action="{{ route('cocokan.exportCostperpartai') }}" method="get">
                    <div class="row">
                        <div class="col-lg-3">
                            <select name="partai" id="select2" class="form-control pilih_partai">
                                <option value="">Pilih Partai</option>
                                @foreach ($partai as $p)
                                    <option value="{{ $p->nm_partai }}">{{ $p->nm_partai }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-9">
                            <a href="{{ route('cocokan.exportCostpartai') }}"
                                class="btn btn-primary btn-sm float-end mb-2"><i class="fas fa-file-excel"></i>Export
                                All</a>
                            <button type="submit" class="btn btn-primary btn-sm float-end mb-2 me-2"><i
                                    class="fas fa-file-excel"></i>Export</button>
                        </div>
                        <div class="col-lg-12 mt-4">
                            <div id="loadHalaman"></div>
                        </div>
                    </div>
                </form>



            </div>

            <style>
                .modal_sedang {
                    max-width: 600px;
                }
            </style>
            <div class="modal fade" id="detail_data" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog  modal_sedang">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Detail Grading</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="load_grade"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>


                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="detail_data2" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog  modal_sedang">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Detail Pengiriman</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="load_grade2"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>


                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade " id="detail_cabut_tes" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Detail Cabut</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="load_cabut"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>


                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade " id="detail_bk_tes" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Detail Bk awal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="load_bk"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>


                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade " id="detail_cetak_tes" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Detail Cetak</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="load_cetak"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>


                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade " id="detail_sortir_tes" tabindex="-1" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Detail Cetak</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="load_sortir"></div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light-secondary" data-bs-dismiss="modal">
                                <i class="bx bx-x d-block d-sm-none"></i>
                                <span class="d-none d-sm-block">Close</span>
                            </button>


                        </div>
                    </div>
                </div>
            </div>


        </section>








        @section('scripts')
            <script>
                pencarian('tbl1input', 'tbl1')
            </script>
            <script>
                get_opr();

                function get_opr() {
                    $.ajax({
                        type: "get",
                        url: "{{ route('summary.get_operasional') }}",
                        beforeSend: function() {
                            $('#cost_opr').html("loading...");
                        },
                        success: function(response) {
                            $('#cost_opr').html(response);
                        }
                    });
                }
            </script>
            <script>
                function numberFormat(initialValue) {
                    return {
                        formattedNumber: new Intl.NumberFormat().format(initialValue),
                        formatNumber() {
                            // Hapus karakter non-digit dan simpan nomor mentah
                            let rawNumber = this.formattedNumber.replace(/\D/g, '');

                            // Format nomor dengan pemisah ribuan
                            this.formattedNumber = new Intl.NumberFormat().format(rawNumber);
                        }
                    };
                }
            </script>
            <script>
                $(document).ready(function() {
                    $('.pilih_partai').change(function(e) {
                        e.preventDefault();
                        var partai = $(this).val();
                        $.ajax({
                            type: "get",
                            url: "{{ route('cocokan.getCostpartai') }}",
                            data: {
                                partai: partai
                            },
                            beforeSend: function() {
                                $('#loadHalaman').html("loading...");
                            },
                            success: function(response) {
                                $('#loadHalaman').html(response);

                            }
                        });

                    });
                    $(document).on('click', '.detail_grade', function(e) {
                        e.preventDefault();
                        var nm_partai = $(this).attr('nm_partai');
                        $.ajax({
                            type: "get",
                            url: "{{ route('cocokan.detailGrade') }}",
                            data: {
                                nm_partai: nm_partai
                            },
                            success: function(response) {
                                $("#load_grade").html(response);
                                $('#tableHalaman').DataTable({
                                    "searching": true,
                                    "autoWidth": true,
                                    "paging": true,
                                    "ordering": true
                                });

                            }
                        });

                    });
                    $(document).on('click', '.detail_grade2', function(e) {
                        e.preventDefault();
                        var nm_partai = $(this).attr('nm_partai');
                        $.ajax({
                            type: "get",
                            url: "{{ route('cocokan.detailGrade2') }}",
                            data: {
                                nm_partai: nm_partai
                            },
                            success: function(response) {
                                $("#load_grade2").html(response);
                                $('#tableHalaman').DataTable({
                                    "searching": true,
                                    "autoWidth": true,
                                    "paging": true,
                                    "ordering": true
                                });

                            }
                        });

                    });
                    $(document).on('click', '.detail_bk', function(e) {
                        e.preventDefault();
                        var nm_partai = $(this).attr('nm_partai');
                        $.ajax({
                            type: "get",
                            url: "{{ route('cocokan.getDetailbkpartai') }}",
                            data: {
                                nm_partai: nm_partai
                            },
                            success: function(response) {
                                $("#load_bk").html(response);
                                $('#tableHalamanbk').DataTable({
                                    "searching": true,
                                    "autoWidth": true,
                                    "paging": true,
                                    "ordering": true
                                });

                            }
                        });

                    });
                    $(document).on('click', '.detail_cabut', function(e) {
                        e.preventDefault();
                        var nm_partai = $(this).attr('nm_partai');
                        $.ajax({
                            type: "get",
                            url: "{{ route('cocokan.getDetailCabutpartai') }}",
                            data: {
                                nm_partai: nm_partai
                            },
                            success: function(response) {
                                $("#load_cabut").html(response);
                                $('#tableHalamanCabut').DataTable({
                                    "searching": true,
                                    "autoWidth": true,
                                    "paging": true,
                                    "ordering": true
                                });

                            }
                        });

                    });
                    $(document).on('click', '.detail_cetak', function(e) {
                        e.preventDefault();
                        var nm_partai = $(this).attr('nm_partai');
                        $.ajax({
                            type: "get",
                            url: "{{ route('cocokan.getDetailCetakpartai') }}",
                            data: {
                                nm_partai: nm_partai
                            },
                            success: function(response) {
                                $("#load_cetak").html(response);
                                $('#tableHalamanCetak').DataTable({
                                    "searching": true,
                                    "autoWidth": true,
                                    "paging": true,
                                    "ordering": true
                                });

                            }
                        });

                    });
                    $(document).on('click', '.detail_sortir', function(e) {
                        e.preventDefault();
                        var nm_partai = $(this).attr('nm_partai');
                        $.ajax({
                            type: "get",
                            url: "{{ route('cocokan.getDetailSortirpartai') }}",
                            data: {
                                nm_partai: nm_partai
                            },
                            success: function(response) {
                                $("#load_sortir").html(response);
                                $('#tableHalamanCetak').DataTable({
                                    "searching": true,
                                    "autoWidth": true,
                                    "paging": true,
                                    "ordering": true
                                });

                            }
                        });

                    });
                });
            </script>
        @endsection

    </x-slot>
</x-theme.app>
