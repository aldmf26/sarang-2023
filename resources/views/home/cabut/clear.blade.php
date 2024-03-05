<x-theme.app title="Table" table="Y" sizeCard="4">
    <x-slot name="cardHeader">
        <h6>Tambahkan Ke Database Perbox</h6>
    </x-slot>

    <x-slot name="cardBody">
   
        <section class="row">
            <div class="col-lg-12">
                <form action="{{ route('cabut.clearSave') }}" method="post">
                    @csrf
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label for="">Password</label>
                                <input value="{{ old('password') }}" required type="password" name="password"
                                    placeholder="password" class="form-control">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <ldabel for="">Bulan</ldabel>
                                <input value="{{ old('bulan') }}" required type="text" name="bulan"
                                    placeholder="bulan" class="form-control">
                            </div>
                        </div> 
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="">Tahun</label>
                                <input value="{{ old('tahun') }}" required type="text" name="tahun"
                                    placeholder="tahun" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button class="float-end btn-block btn btn-sm btn-primary" type="submit">Simpan</button>
                </form>
            </div>
        </section>

    </x-slot>
</x-theme.app>
