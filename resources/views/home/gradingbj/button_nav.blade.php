<h6 class="float-start">{{ $title }}</h6>

<x-theme.button href="#" icon="fa-plus" addClass="float-end ambil_box_kecil" teks="Ambil Box Kecil" />
<x-theme.button href="{{ route('gradingbj.add') }}" icon="fa-plus" addClass="float-end" teks="Ambil dari ctk" />
<x-theme.button modal="Y" idModal="import" href="#" icon="fa-upload" addClass="float-end" teks="Import" />
<form action="{{ route('gradingbj.import') }}" enctype="multipart/form-data" method="post">
    @csrf
    <x-theme.modal size="modal-lg" idModal="import" title="Import Pengiriman">
        <div class="row">
            <table>
                <tr>
                    <td width="100" class="pl-2">
                        <img width="80px" src="{{ asset('/img/1.png') }}" alt="">
                    </td>
                    <td>
                        <span style="font-size: 20px;"><b> Download Excel template</b></span><br>
                        File ini memiliki kolom header dan isi yang sesuai dengan data menu
                    </td>
                    <td>
                        <a href="{{ route('gradingbj.template') }}" class="btn btn-primary btn-sm"><i
                                class="fa fa-download"></i> DOWNLOAD
                            TEMPLATE</a>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <hr>
                    </td>
                </tr>
                <tr>
                    <td width="100" class="pl-2">
                        <img width="80px" src="{{ asset('/img/2.png') }}" alt="">
                    </td>
                    <td>
                        <span style="font-size: 20px;"><b> Upload Excel template</b></span><br>
                        Setelah mengubah, silahkan upload file.
                    </td>
                    <td>
                        <input type="file" name="file" class="form-control">
                    </td>
                </tr>
            </table>

        </div>
    </x-theme.modal>
</form>

<style>
    .modal-lg-max {
        max-width: 1500px
    }
</style>

<form action="{{ route('gradingbj.create_grading') }}" method="post">
    @csrf
    <x-theme.modal size="modal-lg-max" idModal="grading" title="Grading">
        <div id="load_grading"></div>
    </x-theme.modal>
</form>

<x-theme.modal idModal="detail" btnSave="T" title="Detail">
    <div id="load_detail"></div>
</x-theme.modal>

<form action="{{ route('gradingbj.create_ambil_box_kecil') }}" method="post">
    @csrf
    <x-theme.modal idModal="ambil_box_kecil" size="modal-md" title="Ambil Box Kecil">
        <div id="load_ambil_box_kecil"></div>
    </x-theme.modal>
</form>

@section('scripts')
    <script>
        pencarian('pencarianGudang', 'gudang')

        function tekanBawah() {
            function navigateInputs(className, e) {
                if (e.keyCode === 40 || e.keyCode === 38) {
                    e.preventDefault();
                    var $input = $(e.target);
                    var currentCount = parseInt($input.attr('count'));
                    var direction = (e.keyCode === 40) ? 1 : (e.keyCode === 38) ? -1 :
                        0; // 1 for down arrow, -1 for up arrow

                    var $nextInput = $input.closest('tr').siblings('tr').find('.' + className + '[count="' + (currentCount +
                        direction) + '"]');
                    if ($nextInput.length) {
                        $nextInput.focus();

                        // Memeriksa jika kelas yang di-fokus adalah pcsAwal atau grAwal, lalu memilih seluruh teks di dalamnya.
                        if ($nextInput.hasClass('pcs') || $nextInput.hasClass('gr')) {
                            $nextInput.select(); // Memilih seluruh teks dalam input.
                        }
                    }
                }
            }

            $('table').on('keydown', 'input', function(e) {
                var className = $(this).attr('class').split(' ')[
                    1]; // Mendapatkan kelas kedua (contoh: 'nolot' atau 'nobox')
                navigateInputs(className, e);
            });
        }

        $(document).on('click', '.grading', function(e) {
            e.preventDefault();

            const no_grading = $(this).attr('no_grading')
            $.ajax({
                type: "GET",
                url: "{{ route('gradingbj.load_grading') }}",
                data: {
                    no_grading: no_grading
                },
                success: function(r) {
                    $("#load_grading").html(r);
                    $('#grading').modal('show')
                    tekanBawah()


                }
            });
        })

        $(document).on('click', '.detail', function(e) {
            e.preventDefault();

            const no_grading = $(this).attr('no_grading')
            $.ajax({
                type: "GET",
                url: "{{ route('gradingbj.load_detail') }}",
                data: {
                    no_grading: no_grading
                },
                success: function(r) {
                    $("#load_detail").html(r);
                    $('#detail').modal('show')
                }
            });
        })

        $(".ambil_box_kecil").click(function(e) {
            e.preventDefault();
            $('#ambil_box_kecil').modal('show')
            $.ajax({
                type: "GET",
                url: "{{ route('gradingbj.load_ambil_box_kecil') }}",
                success: function(r) {
                    $("#load_ambil_box_kecil").html(r);
                }
            });
        });

        $(document).on('change', '.selectGrade', function() {
            const grade = $(this).val()
            $('.gradeInput').text(grade);
            $.ajax({
                type: "GET",
                url: "{{ route('gradingbj.get_select_grade') }}",
                data: {
                    grade: grade
                },
                dataType: "json",
                success: function(r) {
                    $('.pcsTtl_ambil').val(r.pcs)
                    $('.grTtl_ambil').val(r.gr)
                    $('.ttlrpTtl_ambil').val(r.ttl_rp.toLocaleString('id-ID'))
                }
            });
        })

        function validasiAmbil(inputClass, totalInputName) {
            $(document).on('keyup', '.' + inputClass, function() {
                let totalPcs = 0;
                $('.' + inputClass).each(function() {
                    let pcsValue = parseInt($(this).val()) || 0;
                    totalPcs += pcsValue;
                });
                const ttlPcsGrade = $('.' + totalInputName).val()
                if (totalPcs > ttlPcsGrade) {
                    alert('Ambil Melebihi stok gudang bj')
                }
            });
        }
        validasiAmbil('pcsAmbil', 'pcsTtl_ambil')
        validasiAmbil('grAmbil', 'grTtl_ambil')
        // Event listener untuk input pcsAmbil
    </script>
@endsection
