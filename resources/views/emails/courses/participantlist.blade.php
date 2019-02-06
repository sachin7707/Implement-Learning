@extends('layouts.email', [
    'course' => $course,
    'footer' => json_decode($footer->text)
])
@section('title', 'Deltagerliste')

@section('participantlist')
    we just need some data here... this is not displayed anywhere though :)

    // NOTE: $trainer + $daysTo are also available

@endsection