<x-theme.app title="No Table" table="T">
    <x-slot name="slot">

        <form action="" method="post">
            @csrf
            <x-theme.multiple-input>
                <div class="col-lg-2">
                    <input type="text" class="form-control" name="nm_grade[]">
                </div>
            </x-theme.multiple-input>
            <button type="submit">save</button>
        </form>

    </x-slot>

</x-theme.app>