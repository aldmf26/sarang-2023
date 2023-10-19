<x-theme.app title="{{ $title }}" table="Y" sizeCard="6">
    <x-slot name="cardHeader">
        <form action="">
            <div class="row">
                <div class="col-lg-6">
                    <h6 class="float-start mt-1">{{ $title }}: {{date('d M y',strtotime($tanggal))}}</h6>
                </div>
                <div class="col-lg-5">
                    <input type="date" name="tgl" id="tglAbsen" class="form-control float-end" value="{{$tanggal}}">
                </div>
                <div class="col-lg-1">
                    <button type="submit" class="btn btn-sm btn-primary float-end">View</button>
                </div>
            </div>
        </form>
    </x-slot>

    <x-slot name="cardBody">
        <section class="row">
            <div id="dt-absen">

            </div>
        </section>


        @section('scripts')
        <script>
            $(".select3").select2()

            load_absen();

            function load_absen() {
                var tgl = $("#tglAbsen").val();
                // alert(tgl)
                var url = "{{ route('absen.tabelAbsen') }}?tgl=" + tgl
                $("#dt-absen").load(url, "data", function(response, status,
                    request) {
                    this; // dom element
                    $('#tb_absen').DataTable({

                        "searching": false,
                        scrollY: '400px',
                        scrollX: false,
                        scrollCollapse: false,
                        "stateSave": true,
                        "autoWidth": true,
                        "paging": false,
                    });
                });
                
                
            }
            $(document).on('click', '.save_absen', function(event) {
                    // var id_karyawan = $("#id_karyawan").val();
                    var id_anak = $(this).attr('id_anak');
                    var tgl = $(this).attr('tgl');
                    var ket = $(this).attr('ket');
                    var id_absen = $(this).attr('id_absen');

                    $.ajax({
                        type: "get",
                        url: "/home/absen/SaveAbsen",
                        data: {
                            id_anak:id_anak,
                            tgl:tgl,
                            ket:ket,
                            id_absen:id_absen,
                        },
                        success: function (r) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'success',
                                title: 'Data absen telah ditambahkan'
                            });
                            load_absen();
                        }
                    });
                    
                });
                $(document).on('click', '.delete_absen', function(event) {
                    // var id_karyawan = $("#id_karyawan").val();
                    var id_anak = $(this).attr('id_anak');
                    var tgl = $(this).attr('tgl');
                    var ket = $(this).attr('ket');
                    var id_absen = $(this).attr('id_absen');

                    $.ajax({
                        type: "get",
                        url: "/home/absen/delete_absen",
                        data: {
                            id_anak:id_anak,
                            tgl:tgl,
                            ket:ket,
                            id_absen:id_absen,
                        },
                        success: function (r) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                icon: 'success',
                                title: 'Data absen telah dihapus'
                            });
                            load_absen();
                        }
                    });
                    
                });
        </script>
        @endsection
    </x-slot>
</x-theme.app>