@props([
    'title' => '',
    'kategori' => 'cetak',
])
<x-theme.button modal="Y" idModal="view" icon="fa-calendar-week" addClass="float-end" teks="View" />
<form action="" method="get">
    <x-theme.modal title="Filter Tanggal" idModal="view">
        <div class="row">
            <div class="col-lg-3">Filter</div>
            <div class="col-lg-1">:</div>
            <div class="col-lg-8">
                <select name="period" id="" class="form-control filter_tgl">
                    <option value="daily">Hari ini</option>
                    <option value="mounthly">Bulan </option>
                    <option value="years">Tahun</option>
                    <option value="costume">Custom</option>
                </select>
            </div>
            <div class="col-lg-4 mt-2"></div>
            <div class="col-lg-4 costume_muncul mt-2">
                <label for="">Dari</label>
                <input type="date" name="tgl1" class="form-control tgl">
            </div>
            <div class="col-lg-4 costume_muncul mt-2">
                <label for="">Sampai</label>
                <input type="date" name="tgl2" class="form-control tgl">
            </div>
            <div class="col-lg-4 bulan_muncul mt-2">
                <label for="">Bulan</label>
                <select name="bulan" id="bulan" class="selectView bulan">
                    @php
                        $listBulan = DB::table('bulan')->get();
                    @endphp
                    @foreach ($listBulan as $l)
                        <option value="{{ $l->bulan }}" {{ (int) date('m') == $l->bulan ? 'selected' : '' }}>
                            {{ $l->nm_bulan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-4 bulan_muncul mt-2">
                <label for="">Tahun</label>
                <select name="tahun" id="" class="selectView bulan">
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024" selected>2024</option>
                    <option value="2025" selected>2025</option>
                </select>
            </div>
            <div class="col-lg-8 tahun_muncul mt-2">
                <label for="">Tahun</label>
                <select name="tahun" id="" class="selectView tahun">
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                </select>
            </div>
        </div>
        <input type="hidden" name="kategori" value="{{ $kategori }}">

    </x-theme.modal>
</form>
