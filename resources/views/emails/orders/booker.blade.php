@extends('layouts.email', [
    'courses' => $courses,
    'participants' => $order->company->participants
])
@section('title', 'KVITTERING')

@section('booker')
    this text is not used :)

    @section('intro_block')
        @foreach ($courses as $course)
            <a style="text-decoration: underline;transition: opacity 0.1s ease-in;color: #000;" href="http://cm.konform.com/t/d-l-njtully-l-y/">{{ $course->name }}</a>
            @if (! $loop->last && count($courses) > 1)
                <br>
            @endif
        @endforeach
    @endsection
@endsection