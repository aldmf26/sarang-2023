<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">
        <div class="d-flex justify-content-between">
            <h6 class="">{{ $title }}</h6>
            <div>
                <a href="{{ route('hrga6_1.index', ['area' => $area]) }}" class="btn btn-info"><i
                        class="fas fa-arrow-left"></i>Kembali</a>
            </div>
        </div>
    </x-slot>
    <x-slot name="cardBody">
      

    </x-slot>

</x-theme.app>
