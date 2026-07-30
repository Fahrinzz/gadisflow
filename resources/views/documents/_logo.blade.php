{{-- GADIS KREATIF logo. Uses public/images/logo.* if present, else styled text. --}}
@php
    $logo = null;
    foreach (['png', 'jpg', 'jpeg', 'webp', 'svg'] as $ext) {
        if (file_exists(public_path("images/logo.$ext"))) {
            $logo = "images/logo.$ext";
            break;
        }
    }
@endphp
@if($logo)
    <img src="{{ asset($logo) }}" alt="GADIS KREATIF">
@else
    <div class="l1">GADIS</div>
    <div class="l2">KREATIF</div>
@endif
