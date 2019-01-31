@extends('layouts.email', ['courses' => $courses, 'footer' => $footer])
@section('title', 'DELTAGER EMAIL')
@section('intro', 'Hej ' . $participant->name . ', du blevet tilmeldt et eller flere af implement Learning Institute kurser, i denne email finder du praktisk information omkring disse:')

@section('participant')
    we just need some data here... this is not displayed anywhere though :)

    @section('before_course', $beforeCourse)

    @section('location_and_dates')
        asd
    @endsection
@endsection

<!--<div>Calendar link: <a href="{{$calendarUrl}}">{{$calendarUrl}}</a></div>-->