<form action="{{ route('nota') }}" method="post" enctype="multipart/form-data">
    @csrf
    <input type="file" name="image">

    <label for="file">Keterangan</label>
    <input type="text" name="ket">
    <button type="submit">Save</button>
</form>

<table>
    @foreach ($data as $i => $d)
    <tr>
        <td>{{ $i+1 }}</td>
        <td ><img style="width: 40%" src="{{ asset('uploads/' . $d['image_name']) }}" alt=""></td>
        <td>{{ $d['ket'] }}</td>
    </tr>
    @endforeach
</table>