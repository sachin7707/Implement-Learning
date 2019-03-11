@extends('layouts.email', [
    'course' => $course,
    'footer' => json_decode($footer->text)
])
@section('title', $language === 'da' ? 'Deltagerliste' : 'Participant list')

@section('emailcontent')

@endsection
