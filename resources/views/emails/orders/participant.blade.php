@extends('layouts.email', [
    'courses' => $order->courses,
    'footer' => json_decode($footer->text),
    'language' => $language,
])
@section('title', $language === 'da' ? 'DELTAGER EMAIL' : 'PARTICIPANT EMAIL')
@section('intro', str_replace('$name', $participant->name, $intro->text))

@section('participant')
    we just need some data here... this is not displayed anywhere though :)

    @section('before_course', 'test')

    @section('location_and_dates')
        asd
    @endsection
@endsection

<!--<div>Calendar link: <a href="{{$calendarUrl}}">{{$calendarUrl}}</a></div>-->