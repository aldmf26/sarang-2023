<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <h6>{{ $title }}</h6>

    </x-slot>

    <x-slot name="cardBody">
        <form action="{{ route('kelas.create_grade') }}" method="post">
            @csrf
            <x-theme.multiple-input>
                <div class="col-lg-3">
                    <input type="text" name="nm_grade[]" placeholder="nm grade" class="form-control">
                </div>
            </x-theme.multiple-input>
            <button type="submit" class="btn btn-info mt-3">save</button>
        </form>
    </x-slot>

</x-theme.app>
