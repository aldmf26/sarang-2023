<x-theme.button modal="Y" idModal="view" icon="fa-calendar-week" addClass="float-end"
                    teks="View" />
                <form action="">
                    <x-theme.modal title="View Rekap" idModal="view">
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="">Bulan dibayar</label>
                                <select name="bulan" class="form-control selectView" id="">
                                    <option value="">- Pilih Bulan -</option>
                                    @php
                                        $bulan = DB::table('bulan')->get();
                                    @endphp
                                    @foreach ($bulan as $b)
                                        <option value="{{ $b->bulan }}">{{ strtoupper($b->nm_bulan) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label for="">Tahun</label>
                                <select name="tahun" class="form-control selectView" id="">
                                    <option value="">- Pilih Tahun -</option>
                                    @php
                                        $tahun = [2022,2023,2024];
                                    @endphp
                                    @foreach ($tahun as $b)
                                        <option value="{{ $b }}">{{ strtoupper($b) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </x-theme.modal>
                </form>