@props([
    'route' => '',
    'name' => '',
    'tgl1' => '',
    'tgl2' => '',
    'id_proyek' => '',
])
<form action="{{ route($route) }}" method="get">
    <div class="modal" id="delete" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title white" id="myModalLabel120">Hapus Data
                    </h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="feather feather-x" data-darkreader-inline-stroke=""
                            style="--darkreader-inline-stroke: currentColor;">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <h5 class="text-danger ms-4 mt-4"><i class="fas fa-trash"></i> Apakah yakin ingin dihapus ?</h5>
                        <input type="hidden" class="no_nota" name="{{ $name }}">
                        <input type="hidden" name="tgl1" value="{{ $tgl1 ?? '' }}">
                        <input type="hidden" name="tgl2" value="{{ $tgl2 ?? '' }}">
                        <input type="hidden" name="id_proyek" value="{{ $id_proyek ?? '' }}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</form>
@section('scripts')
    <script>
        $(document).on('click', '.delete_nota', function() {
            var no_nota = $(this).attr('no_nota');
            $('.no_nota').val(no_nota);
        })
    </script>
@endsection
