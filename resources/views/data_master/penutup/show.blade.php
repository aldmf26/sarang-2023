<x-theme.app title="{{ $title }}" table="Y" sizeCard="12">
    <x-slot name="cardHeader">

        <h6 class=" mt-1">{{ $title }}</h6>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            @foreach ($pengawas as $p)
                <li class="nav-item" role="presentation">
                    <a class="nav-link @if ($loop->first) active @endif" id="{{$p->name}}-tab" data-bs-toggle="tab" href="#{{$p->name}}" role="tab"
                        aria-controls="{{$p->name}}" aria-selected="@if ($loop->first) true @else false @endif">{{ strtoupper($p->name) }}</a>
                </li>
            @endforeach
        </ul>
        <div class="tab-content" id="myTabContent">
            @foreach ($pengawas as $p)
                <div class="tab-pane fade @if ($loop->first) show active @endif" id="{{$p->name}}" role="tabpanel" aria-labelledby="{{$p->name}}-tab">
                    {{ $p->name }}
                </div>
            @endforeach
        </div>
        
    </x-slot>

    <x-slot name="cardBody">

        <section class="row">

        </section>
    </x-slot>
</x-theme.app>
