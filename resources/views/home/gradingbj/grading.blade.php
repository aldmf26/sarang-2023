<ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link active" id="dForm-tab" data-bs-toggle="tab" href="#dForm" role="tab" aria-controls="home"
            aria-selected="true">D</a>
    </li>
    {{-- <li class="nav-item" role="presentation">
        <a class="nav-link" id="vForm-tab" data-bs-toggle="tab" href="#vForm" role="tab" aria-controls="profile"
            aria-selected="false" tabindex="-1">V</a>
    </li> --}}

</ul>
<div class="tab-content" id="myTabContent">
    @include('home.gradingbj.konten_grading', ['form' => 'dForm'])
    {{-- @include('home.gradingbj.konten_grading', ['form' => 'vForm']) --}}
</div>
