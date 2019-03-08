@extends('layouts.email', [
    'courses' => $order->courses,
    'participants' => $order->company->participants,
    'footer' => json_decode($footer->text),
    'language' => $language,
])
@section('title', $language === 'da' ? 'KVITTERING' : 'RECEIPT')

@section('booker')
    this text is not used :)

    @section('intro_block')
        <p class="size-15" style="Margin-top: 0;Margin-bottom: 0;font-family: calibri,carlito,pt sans,trebuchet ms,sans-serif;font-size: 15px;line-height: 23px;" lang="x-size-15"><span class="font-calibri"><span style="color:#000000">
            @if ($order->on_waitinglist == 0)
                @if ($language === 'da')
                    Du har tilmeldt deltagerne til kurserne:
                @else
                    You have added the following participants to the courses:
                @endif
            @else
                @if ($language === 'da')
                    Du har tilmeldt deltagerne til venteliste på kurserne:
                @else
                    You have added the following participants to the waitinglist for the courses:
                @endif
            @endif
        </span></span></p>
        @foreach ($order->courses as $course)
            <a style="text-decoration: underline;transition: opacity 0.1s ease-in;color: #000;" href="{{ $course->getLink() }}">{{ $course->getTitle() }} ({{ $course->getLanguage() }})</a>
            @if (! $loop->last && count($courses) > 1)
                <br>
            @endif
        @endforeach
    @endsection
@endsection
