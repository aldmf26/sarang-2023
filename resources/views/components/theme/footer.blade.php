<footer>
    <div class="container print_hilang">
        <div class="footer clearfix mb-0 text-sm text-muted">
            <div class="float-start">
                <p>2023 &copy; PTAGAFOOD</p>
            </div>
            <div class="float-end">
                <p>Crafted with <span class="text-danger"><i class="bi bi-heart"></i></span> by <a
                        href="https://ptagafood.com">AgrikaGroup</a></p>
            </div>
        </div>
    </div>
</footer>
</div>
</div>
<script src="{{ asset('theme') }}/assets/js/bootstrap.js"></script>
{{-- <script src="{{ asset('theme') }}/assets/js/app.js"></script> --}}
<script src="{{ asset('theme') }}/assets/extensions/jquery/jquery.min.js"></script>
<script src="{{ asset('theme') }}/assets/extensions/choices.js/public/assets/scripts/choices.js"></script>

<script src="{{ asset('theme') }}/assets/js/pages/form-element-select.js"></script>
<script src="{{ asset('theme') }}/assets/extensions/toastify-js/src/toastify.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
{{-- <script src="{{ asset('theme') }}/assets/js/select2.min.js"></script> --}}
<script src="https://cdn.datatables.net/v/bs5/dt-1.12.1/datatables.min.js"></script>
<script src="{{ asset('theme') }}/assets/js/pages/datatables.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('theme') }}/assets/js/pages/horizontal-layout.js"></script>
<script src="{{ asset('theme') }}/assets/extensions/dragula/dragula.min.js"></script>
<script src="{{ asset('theme') }}/assets/js/pages/ui-todolist.js"></script>


<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    $(document).on('click', '.akses_h', function() {
        var id_user = $(this).attr('id_user');
        if ($('.akses_h' + id_user).prop("checked") == true) {
            $('.open_check' + id_user).removeAttr('disabled');
        } else {
            $('.open_check' + id_user).prop('disabled', true);

        }

    });

    // untuk file upload ada preview
    $(document).on('change', '#image', function() {
        var file = this.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(event) {
                $('#image-preview').html('<img src="' + event.target.result +
                    '" class="img-fluid">');
            }
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').html('');
        }
    })
    // ----

    $('.select2').select2({
        dropdownParent: $('#tambah .modal-content')
    });
    $('.select2-tambah2').select2({
        dropdownParent: $('#tambah2 .modal-content')
    });
    $('.selectView').select2({
        dropdownParent: $('#view .modal-content')
    });
    $('.select2-edit').select2({
        dropdownParent: $('#edit .modal-content')
    });


    $('.costume_muncul').hide();
    $('.bulan_muncul').hide();
    $('.tahun_muncul').hide();
    $('.tgl').prop('disabled', true);
    $(document).on("change", ".filter_tgl", function() {
        var period = $(this).val();
        $('.costume_muncul').toggle(period === 'costume');
        $('.tgl').prop('disabled', period !== 'costume');

        $('.bulan_muncul').toggle(period === 'mounthly');
        $('.bulan').prop('disabled', period !== 'mounthly');

        $('.tahun_muncul').toggle(period === 'years');
        $('.tahun').prop('disabled', period !== 'years');


    });

    $('#select2').select2({});
    $('.select2_add').select2({});
    $('.select2_readonly').select2({
        disabled: true
    });

    function plusRow(count, classPlus, url) {
        $(document).on("click", "." + classPlus, function() {
            count = count + 1;
            $.ajax({
                url: `${url}?count=` + count,
                type: "GET",
                success: function(data) {
                    $("#" + classPlus).append(data);
                    $(".select2-add").select2({
                        dropdownParent: $('#tambah .modal-content'),
                        dropdownParent: $('#tambah2 .modal-content')

                    });
                    $('.select2-aja').select2()
                    $(".select3-add").select2({
                        dropdownParent: $('#tambah .modal-content'),
                    })
                },
            });
        });

        $(document).on('click', '.remove_baris', function() {
            var delete_row = $(this).attr("count");
            $(".baris" + delete_row).remove();

        })
    }

    function plusRow2(count, classPlus, formModal, url) {
        $(document).on("click", "." + classPlus, function() {
            count = count + 1;
            $.ajax({
                url: `${url}?count=` + count,
                type: "GET",
                success: function(data) {
                    $("#" + classPlus).append(data);
                    $(".select2-plusRow2").select2({
                        dropdownParent: $(`#${form} .modal-content`)
                    });
                },
            });
        });

        $(document).on('click', '.remove_baris', function() {
            var delete_row = $(this).attr("count");
            $(".baris" + delete_row).remove();

        })
    }

    function detail(kelas, attr, link, load) {
        $(document).on('click', `.${kelas}`, function() {
            var id = $(this).attr(`${attr}`)
            $.ajax({
                type: "GET",
                url: `${link}/${id}`,
                success: function(r) {
                    $(`#${load}`).html(r);
                    $('.select2-edit').select2({
                        dropdownParent: $('#edit .modal-content')
                    });
                    $('#tableDetail').DataTable({
                        "paging": true,
                        "pageLength": 10,
                        "lengthChange": true,
                        "stateSave": true,
                        "searching": true,
                    });
                }
            });
        })
    }

    function formatRibuan(id) {
        // Fungsi untuk memformat nilai input saat pengguna mengetik
        $(`#${id}Input`).on('input', function() {
            var inputValue = $(this).val().replace(/,/g, ''); // Hapus tanda koma
            var formattedValue = formatRupiah(inputValue);
            $(this).val(formattedValue);
        });
        $(`form`).submit(function() {
            var inputValue = $(`#${id}Input`).val().replace(/[,\.]/g, ''); // Hapus tanda koma
            $(`#${id}Input`).val(inputValue); // Setel nilai tanpa tanda koma kembali ke input
        });
    }
    // Function untuk mengubah format angka menjadi format Rupiah
    function formatRupiah(angka) {
        var reverse = angka.toString().split('').reverse().join('');
        var ribuan = reverse.match(/\d{1,3}/g);
        var formatted = ribuan.join(',').split('').reverse().join('');
        return formatted;
    }

    function inputChecked(allId, itemClass) {
        $(document).on('click', '#' + allId, function() {
            $("." + itemClass).prop('checked', $(this).prop('checked'));
        })

        $('.table tbody tr').click(function(event) {
            // Temukan kotak centang di baris ini
            var checkbox = $(this).find('.' + itemClass);

            // Jika yang diklik adalah sel yang bukan kotak centang
            if (!$(event.target).is('.' + itemClass)) {
                // Mengebalikan status kotak centang (ceklis jika sebelumnya tidak terceklis, dan sebaliknya)
                // checkbox.prop('checked', !checkbox.prop('checked'));

                // Memeriksa apakah semua kotak centang di seluruh tabel terceklis atau tidak
                var semuaTerceklis = true;
                $('.table tbody .' + itemClass).each(function() {
                    if (!$(this).prop('checked')) {
                        semuaTerceklis = false;
                        return false; // Keluar dari loop jika ada yang tidak terceklis
                    }
                });

                // Mengatur status kotak centang "cekSemua" berdasarkan apakah semua terceklis atau tidak
                $('#' + allId).prop('checked', semuaTerceklis);
                checkbox.trigger('change');
            }
        });

    }

    function clickSelectInput(classes) {
        $(document).on('click', classes.join(', '), function() {
            // Menyeleksi seluruh teks dalam input field saat diklik
            $(this).select();
        });
    }

    function pencarian(inputId, tblId) {

        $(document).on('keyup', "#" + inputId, function() {
            var value = $(this).val().toLowerCase();
            $(`#${tblId} tbody tr`).filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        })

    }

    function aksiBtn(idForm) {
        $(document).on('submit', idForm, function() {
            $(".button-save").hide();
            $(".btn_save_loading").removeAttr("hidden");
        })
    }

    $(document).on('click', '.selectAll', function() {
        this.select()
    })

    function loadTable(namaTable) {
        $('#' + namaTable).DataTable({
            "paging": true,
            "pageLength": 10,
            fixedHeader: true,
            "lengthChange": true,
            "stateSave": true,
            "searching": true,
        });
    }
    $('#table').DataTable({
        "paging": true,
        "pageLength": 10,
        "lengthChange": true,
        "stateSave": true,
        "searching": true,
    });
    $('#table2').DataTable({
        "paging": true,
        "pageLength": 10,
        "lengthChange": true,
        "stateSave": true,
        "searching": true,
    });

    $('#tableScroll').DataTable({
        "searching": true,
        scrollY: '400px',
        scrollX: true,
        scrollCollapse: true,
        "autoWidth": true,
        "paging": false,
    });

    $('#nanda').DataTable({
        "searching": true,
        scrollY: '400px',
        scrollX: false,
        scrollCollapse: true,
        "autoWidth": true,
        "paging": false,
        "info": false,
        "stateSave": true,
    });

    $('#tablealdi').DataTable({
        "searching": true,
        scrollY: '400px',
        scrollX: false,
        scrollCollapse: false,
        "stateSave": true,
        "autoWidth": true,
        "paging": false,
    });

    $('.button-save-modal').click(function(e) {
        $('.button-save-modal').hide();
        $(".button-save-modal-loading").removeAttr("hidden").show();



    });

    function alertToast(jenis = 'sukses', pesan) {
        var ava = jenis == 'sukses' ? "https://cdn-icons-png.flaticon.com/512/190/190411.png" :
            "https://cdn-icons-png.flaticon.com/512/564/564619.png"
        var bg = jenis == 'sukses' ? "#EAF7EE" : "#FCEDE9"
        $(document).ready(function() {
            Toastify({
                text: pesan,
                duration: 3000,
                position: "center",
                style: {
                    background: bg,
                    color: "#7F8B8B",
                    fontSize: "15px", // Menyesuaikan ukuran teks
                    padding: "15px", // Menyesuaikan jarak padding
                    borderRadius: "10px" // Menambahkan sudut elemen
                },
                close: true,
                avatar: ava
            }).showToast();
        });
    }

    // alpine select2
    function initSelect2() {
        $('.select2-alpine').select2();
        $('.select2-alpine-data').select2();
    }

    window.addEventListener('alpine:init', () => {
        Alpine.directive('select2', (el, {
            expression
        }) => {
            return {
                init() {
                    initSelect2();
                },
                updated() {
                    initSelect2();
                },
            };
        });
    });
</script>
@if (session()->has('sukses'))
    <script>
        $(document).ready(function() {
            Toastify({
                text: "{{ session()->get('sukses') }}",
                duration: 5000,
                position: 'center',
                style: {
                    background: "#EAF7EE",
                    color: "#7F8B8B",
                    fontSize: "15px", // Menyesuaikan ukuran teks
                },
                close: true,
                avatar: "https://cdn-icons-png.flaticon.com/512/190/190411.png"
            }).showToast();
        });
    </script>
@endif
@if (session()->has('error'))
    <script>
        $(document).ready(function() {
            Toastify({
                text: "{{ session()->get('error') }}",
                duration: 5000,
                position: 'center',
                style: {
                    background: "#FCEDE9",
                    color: "#7F8B8B",
                    fontSize: "15px", // Menyesuaikan ukuran teks
                },
                close: true,
                avatar: "https://cdn-icons-png.flaticon.com/512/564/564619.png"
            }).showToast();
        });
    </script>
@endif


{{-- dispatch lievewire --}}
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('showAlert', ([{
            type,
            message
        }]) => {
            alertToast(type, message);
        });
    });
</script>
@yield('scripts')
@yield('js')

@livewireScripts
</body>

</html>
