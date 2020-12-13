@switch(session('message.status'))
    @case('danger')
    @php
        $class = 'alert-danger';
    @endphp
    @break

    @case('info')
    @php
        $class = 'alert-info';
    @endphp
    @break
    @case('warning')
    @php
        $class = 'alert-warning';
    @endphp
    @break

    @case('success')
    @php
        $class = 'alert-success';
    @endphp
    @break
@endswitch

<div class="alert alert-icon {{ $class }} alert-dismissible" role="alert">
    <button type="button" class="close" data-dismiss="alert"></button>
    {{ session('message.body') }}
</div>
