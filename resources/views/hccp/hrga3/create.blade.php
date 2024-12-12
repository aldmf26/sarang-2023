<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
        </div>
    </x-slot>
    <x-slot name="cardBody">
        <form action="{{ route('hrga1.store') }}" method="post">
            <div class="row">
                <div class="col-lg-4">
                    <label for="">Nama Karyawan</label>
                    <select name="nm_karyawan" class="form-control select2 selectKaryawan" id="">
                        <option value="">- Pilih Karyawan -</option>
                        @foreach ($karyawans as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2">
                    <label for="">Usia</label>
                    <input readonly type="text" name="usia" class="form-control">
                </div>
                <div class="col-lg-2">
                    <label for="">Jenis Kelamin</label>
                    <input readonly type="text" name="j_kelamin" class="form-control">
                </div>
                <div class="col-lg-4">
                    <label for="">Posisi</label>
                    <input readonly type="text" name="posisi" class="form-control">
                </div>
            </div>
            @csrf
            <a class="btn btn-md btn-info" href="{{ route('hrga1.index') }}">Cancel</a>
            <button class="btn btn-md float-end btn-primary" type="submit">Simpan</button>
        </form>

        @section('scripts')
            <script>
                $(document).ready(function() {
                    $('.select2').select2();

                    $('.selectKaryawan').on('change', function() {
                        var id = $(this).val();
                        $.ajax({
                            type: "GET",
                            url: "{{ route('hrga3.getKaryawan') }}",
                            data: {
                                id: id
                            },
                            dataType: "json",
                            success: function(response) {
                                $('input[name="usia"]').val(response.usia);
                                $('input[name="j_kelamin"]').val(response.jenis_kelamin);
                                $('input[name="posisi"]').val(response.posisi);
                            }
                        });
                    });
                });
            </script>
        @endsection
    </x-slot>

</x-theme.app>
