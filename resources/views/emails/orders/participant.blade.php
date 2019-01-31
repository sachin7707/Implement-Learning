@extends('layouts.email', ['courses' => $courses, 'footer' => $footer])
@section('title', 'DELTAGER EMAIL')
@section('intro', str_replace('$name', $participant->name, $intro))

@section('participant')
    we just need some data here... this is not displayed anywhere though :)

    @section('before_course', 'test')

    @section('location_and_dates')
        asd
    @endsection
@endsection

<!--<div>Calendar link: <a href="{{$calendarUrl}}">{{$calendarUrl}}</a></div>-->