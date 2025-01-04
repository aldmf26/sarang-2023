<x-theme.app title="{{ $title }}" table="T">
    <x-slot name="slot">
        <h6>{{ $title }}</h6>
        <hr>
        <div class="row">
            @foreach ($divisis as $d)
                <div class="col-lg-3">
                    <a wire:navigate href="{{ route("$divisi.index", ['divisi' => $d->id]) }}">
                        <div style="cursor:pointer;background-color: #8c8989" class="card border  text-white">
                            <div class="card-fronsadt">
                                <div class="card-body">
                                    <h5 class="card-titles text-white text-center">
                                        {{ $d->divisi }}
                                        </h6>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach

    </x-slot>
</x-theme.app>
