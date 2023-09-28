<form action="" method="post" enctype="multipart/form-data">
    @csrf
    <div class="form-group">
        <label for="">Import</label>
        <input type="file" name="anakFile" class="form-control">
    </div>
    <button type="submit">Save</button>
</form>