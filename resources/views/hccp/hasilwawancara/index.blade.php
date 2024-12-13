<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="col-lg-12">
            <h6 class="float-start mt-1">{{ $title }}</h6>
            <a href="{{ route('hasilwawancara.create') }}" class="btn btn-primary float-end"><i class="fas fa-plus"></i>
                Data</a>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <section class="row">
            <div class="col-lg-12">
                <div class="load-data"></div>
            </div>


        </section>



        @section('scripts')
            <script>
                $(document).ready(function() {
                    $('.read-more').click(function(e) {
                        e.preventDefault();
                        $(this).next('.full-text').slideToggle();
                        $(this).text($(this).text() === 'Read More' ? 'Show Less' : 'Read More');
                    });

                    function getData() {
                        $.ajax({
                            type: "get",
                            url: "{{ route('hasilwawancara.getData') }}",
                            success: function(response) {
                                $('.load-data').html(response);
                            }
                        });
                    }
                    getData();

                    function save_data(id_anak) {
                        var nama = $('.nama' + id_anak).val();
                        var tgl_lahir = $('.tgl_lahir' + id_anak).val();
                        var tgl_masuk = $('.tgl_masuk' + id_anak).val();
                        var jenis_kelamin = $('.jenis_kelamin' + id_anak).val();
                        var divisi = $('.divisi' + id_anak).val();

                        $.ajax({
                            type: "get",
                            url: "{{ route('hasilwawancara.tambah_data') }}",
                            data: {
                                id_anak: id_anak,
                                nama: nama,
                                tgl_lahir: tgl_lahir,
                                jenis_kelamin: jenis_kelamin,
                                divisi: divisi,
                                tgl_masuk: tgl_masuk
                            },
                            success: function(response) {
                                getData();
                                alertToast('sukses', 'Berhasil ditambahkan');
                            }
                        });

                    }

                    $(document).on("click", ".simpan_data", function(e) {
                        e.preventDefault();
                        var id_anak = $(this).attr('id_anak');

                        save_data(id_anak);

                    });
                });
            </script>
        @endsection
    </x-slot>

</x-theme.app>
