<x-theme.hccp_print :title="$title" :dok="$dok">

    <div class="row">

        <div class="col-sm-12 col-lg-12">
            <table class="table table-bordered table-hover">
                <thead class="">
                    <tr>
                        @php
                            $class = 'text-center align-middle fs-bold';
                        @endphp
                        <th class="{{$class}}">No</th>
                        <th class="{{$class}}">Area</th>
                        <th class="{{$class}}">Limbah / Polusi</th>
                        <th class="{{$class}}">Metode Pembuangan</th>
                        <th class="{{$class}}">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    
                    
                    @foreach ($limbah as $i => $d)
                    <tr x-data="editData({{ $d->id }}, '{{ $d->area }}', '{{ $d->limbah }}', '{{ $d->metode }}', '{{ $d->ket }}')">
                        <td align="center">{{ $loop->iteration }}</td>
                        <td>
                            <span x-show="!editInput">{{ $d->area }}</span>
                           
                        </td>
    
                        <td>
                            <span x-show="!editInput">{{ $d->limbah }}</span>
                           
                        </td>
    
                        <td>
                            <span x-show="!editInput">{{ $d->metode }}</span>
                           
                        </td>
    
                        <td>
                            <span x-show="!editInput">{{ $d->ket }}</span>
                           
                        </td>
    
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-sm-6 col-lg-4">
        </div>
        <div class="col-sm-6 col-lg-4">
            <table style="border: 1px solid black; border-collapse: collapse; width: 100%; margin-top: 10px;">
                <tr>
                    <td style="border: 1px solid black; text-align: center;">Dibuat Oleh:</td>
                    <td style="border: 1px solid black; text-align: center;">Diketahui Oleh:</td>
                </tr>
                <tr>
                    <td style="border: 1px solid black; text-align: center; height: 80px; vertical-align: bottom;">[SPV.
                        GA-IR]</td>
                    <td style="border: 1px solid black; text-align: center; vertical-align: bottom;">[KA.HRGA]</td>
                </tr>
            </table>
        </div>
    </div>

</x-theme.hccp_print>
