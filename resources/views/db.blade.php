<form action="{{ route('dbcreate') }}" method="post">
    @csrf
    <label for="">database</label>
    <input type="text" name="db">
    <button type="submit" >Save</button>
</form>