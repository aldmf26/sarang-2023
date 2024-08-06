<x-theme.app title="{{ $title }}" table="Y" sizeCard="10">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6>{{ $title }}</h6>

        </div>
    </x-slot>

    <x-slot name="cardBody">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th class="dhead">Box Pengiriman</th>
                    <th class="dhead">Grade</th>
                    <th class="dhead text-end">Pcs</th>
                    <th class="dhead text-end">Gr</th>
                    <th></th>
                    <th width="100" class="dhead text-end">Pcs Selisih</th>
                    <th width="100" class="dhead text-end">Gr Selisih</th>
                    <th class="dhead text-end">Total Pcs</th>
                    <th class="dhead text-end">Total Gr</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>P10001</td>
                    <td>S6</td>
                    <td class="text-end">125</td>
                    <td class="text-end">1000</td>
                    <td></td>
                    <td class="text-end">
                        <input type="text" class="text-end form-control" name="pcs_selisih">
                    </td>
                    <td class="text-end">
                        <input type="text" class="text-end form-control" name="gr_selisih">
                    </td>
                    <td class="text-end">120</td>
                    <td class="text-end">950</td>
                </tr>
            
            </tbody>
        </table>

    </x-slot>
</x-theme.app>
