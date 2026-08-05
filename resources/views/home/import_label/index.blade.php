<x-theme.app :title="$title" table="Y" sizeCard="12" cont="container-fluid">
    <x-slot name="cardHeader">
        <h5>{{ $title }}</h5>
    </x-slot>

    <x-slot name="cardBody">
        <div class="row g-3">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Import data Excel</h6>
                    </div>
                    <div class="card-body">
                        @if (session('sukses'))
                            <div class="alert alert-success">{{ session('sukses') }}</div>
                        @endif
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger">{{ $errors->first() }}</div>
                        @endif

                        <form action="{{ route('import-label.store') }}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file">File Excel</label>
                                <input id="file" type="file" name="file" class="form-control"
                                    accept=".xlsx,.xls,.csv" required>
                                <small class="text-muted">Header: partai, box, grade, pcs, gr, bagian</small>
                            </div>
                            <div class="form-check mb-3">
                                <input type="hidden" name="replace_data" value="0">
                                <input id="replace_data" type="checkbox" name="replace_data" value="1"
                                    class="form-check-input" checked>
                                <label class="form-check-label" for="replace_data">
                                    Ganti seluruh data import sebelumnya
                                </label>
                            </div>
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-file-import"></i> Import
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Pengaturan label</h6>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('import-label.print') }}" method="get" target="_blank">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="bagian">Bagian yang Dicetak</label>
                                    <select id="bagian" name="bagian" class="form-select" required>
                                        <option value="">Pilih bagian</option>
                                        @foreach ($bagians as $bagian)
                                            <option value="{{ $bagian }}">{{ $bagian }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Packing otomatis memakai label ukuran kecil.</small>
                                </div>
                                <div class="col-md-6">
                                    <label for="nama_produsen">Nama Produsen</label>
                                    <input id="nama_produsen" name="nama_produsen" class="form-control"
                                        value="{{ old('nama_produsen', $defaults['nama_produsen']) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="tanggal_kedatangan">Tanggal Kedatangan</label>
                                    <input id="tanggal_kedatangan" type="date" name="tanggal_kedatangan"
                                        class="form-control"
                                        value="{{ old('tanggal_kedatangan', $defaults['tanggal_kedatangan']) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="kode_grading">Kode Grading</label>
                                    <input id="kode_grading" name="kode_grading" class="form-control"
                                        value="{{ old('kode_grading', $defaults['kode_grading']) }}" required>
                                </div>
                                <div class="col-12">
                                    <label for="kode_lot">Kode Lot</label>
                                    <input id="kode_lot" name="kode_lot" class="form-control"
                                        value="{{ old('kode_lot', $defaults['kode_lot']) }}" required>
                                </div>
                            </div>
                            <button class="btn btn-primary mt-3" type="submit" {{ $labels->isEmpty() ? 'disabled' : '' }}>
                                <i class="fas fa-print"></i> Print 9 Label per Kertas
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between">
                <h6 class="mb-0">Data Label</h6>
                <span class="badge bg-primary">{{ $labels->count() }} data</span>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Partai</th>
                            <th>No Box</th>
                            <th>Grade</th>
                            <th>Bagian</th>
                            <th class="text-end">Pcs</th>
                            <th class="text-end">Gr</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($labels as $label)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $label->partai }}</td>
                                <td>{{ $label->box }}</td>
                                <td>{{ $label->grade }}</td>
                                <td>{{ $label->bagian }}</td>
                                <td class="text-end">{{ number_format($label->pcs, 0) }}</td>
                                <td class="text-end">{{ number_format($label->gr, 0) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada data import.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </x-slot>
</x-theme.app>
