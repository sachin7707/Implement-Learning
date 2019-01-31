@extends('layouts.email', [
    'courses' => $courses,
    'participants' => $order->company->participants,
    'footer' => $footer,
])
@section('title', 'KVITTERING')

@section('booker')
    this text is not used :)

    @section('intro_block')
        <p class="size-15" style="Margin-top: 0;Margin-bottom: 0;font-family: calibri,carlito,pt sans,trebuchet ms,sans-serif;font-size: 15px;line-height: 23px;" lang="x-size-15"><span class="font-calibri"><span style="color:#000000">Du har tilmeldt deltagerne til kurserne: </span></span></p>
        @foreach ($courses as $course)
            <a style="text-decoration: underline;transition: opacity 0.1s ease-in;color: #000;" href="{{ $course->getLink() }}">{{ $course->getTitle() }}</a>
            @if (! $loop->last && count($courses) > 1)
                <br>
            @endif
        @endforeach
    @endsection
@endsection
