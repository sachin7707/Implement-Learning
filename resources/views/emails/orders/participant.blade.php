@extends('layouts.email', ['courses' => $courses])
@section('title', 'DELTAGER EMAIL')
@section('intro', 'Hej ' . $participant->name . ', du blevet tilmeldt et eller flere af implement Learning Institute kurser, i denne email finder du praktisk information omkring disse:')

@section('participant')
    we just need some data here... this is not displayed anywhere though :)

    @section('before_course')
        Nisl purus in mollis nunc sed id semper. Vitae purus faucibus ornare suspendisse.
        Sit amet venenatis urna cursus eget nunc scelerisque viverra mauris.
        Id volutpat lacus laoreet non. Placerat orci nulla pellentesque dignissim enim sit amet venenatis.
        Dictumst vestibulum rhoncus est pel.
    @endsection

    @section('location_and_dates')
        asd
    @endsection
@endsection

<!--<div>Calendar link: <a href="{{$calendarUrl}}">{{$calendarUrl}}</a></div>-->