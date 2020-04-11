@if(isset($messages))
    @foreach($messages as $message)
        <p>
            {{ $message }}
        </p>
    @endforeach
@endif
