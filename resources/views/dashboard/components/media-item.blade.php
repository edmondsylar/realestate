@php
    $params = [
        'attachment_id' => $media_id,
        'attachment_encrypt' => hh_encrypt($media_id)
    ];
    if (\App::environment('production_ssl')) {
        $media_url = str_replace('http:', 'https:', $media_url);
    }
@endphp
<li>
    <div class="hh-media-item relative" data-params="{{ base64_encode(json_encode($params)) }}" data-delete-url="{{ dashboard_url('delete-media-item') }}">
        <div class="hh-media-thumbnail">
            <img src="{{ $media_url }}" alt="{{ $media_description }}" class="img-fluid">
        </div>
        @if($type === 'normal')
            <a href="javascript:void(0)" class="link link-absolute"
               data-attachment-id="{{ $media_id }}"
               data-url="{{ $media_url }}">&nbsp;</a>
        @else
            <a href="javascript:void(0)" class="link link-absolute" data-toggle="modal"
               data-target="#hh-media-item-modal"
               data-attachment-id="{{ $media_id }}"
               data-url="{{ dashboard_url('media-item-detail') }}">&nbsp;</a>
        @endif
    </div>
</li>
