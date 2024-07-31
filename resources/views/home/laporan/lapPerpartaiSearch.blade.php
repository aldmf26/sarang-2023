<tr>
    <td class="text-end">{{ $partai->nm_partai }}</td>
    <td class="text-end">{{ number_format($partai->pcs_awal ?? 0, 0) }}</td>
    <td class="text-end">{{ number_format($partai->gr_awal ?? 0, 0) }}</td>
    <td class="text-end">{{ number_format($partai->hrga_satuan ?? 0, 0) }}</td>
</tr>
