@extends('layouts.email', [
    'courses' => $order->courses,
    'participants' => $order->company->participants,
    'footer' => json_decode($footer->text),
    'language' => $language,
])
@section('title', $language === 'da' ? 'KVITTERING' : 'RECEIPT')

@section('emailcontent')
    <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
        <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
            <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
            <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                <div style="Margin-left: 46px;Margin-right: 46px;">
                    <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                        <h2 class="size-18" style="Margin-top: 0;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #2f353e;font-size: 17px;line-height: 26px;" lang="x-size-18">{{ $language === 'da' ? 'Tilmeldte deltagere' : 'Participants' }}</h2>
                    </div>
                </div>

            </div>
            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
        </div>
    </div>

    @if (! empty($order->company->participants))
        <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
            <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
                <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
                <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                    <div style="Margin-left: 46px;Margin-right: 46px;">
                        <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                            <p></p>
                            @foreach ($order->company->participants as $participant)
                                <table style="width: 100%; margin-bottom: 10px;">
                                    <tr class="size-15" style="Margin-top: 20px;Margin-bottom: 0;font-size: 15px;line-height: 23px;" lang="x-size-15">
                                        <td style="color:#8e8e8e; width: 25%;">{{ $language === 'da' ? 'Deltager' : 'Participant' }} {{ $loop->iteration }}:</td>
                                        <td style="color:#000000">{{ $participant->name }}</td>
                                        <td style="color:#000000">{{ $participant->title }}</td>
                                    </tr>
                                    <tr class="size-15" style="Margin-top: 20px;Margin-bottom: 0;font-size: 15px;line-height: 23px;" lang="x-size-15">
                                        <td style="color:#8e8e8e; width: 25%"></td>
                                        <td style="color:#000000">{{ $language === 'da' ? 'tlf.' : 'Phone' }} {{ $participant->phone }}</td>
                                        <td style="color:#000000"><a style="text-decoration: underline;transition: opacity 0.1s ease-in;color: #000;" href="mailto: {{ $participant->email }}">{{ $participant->email }}</a></td>
                                    </tr>
                                </table>
                            @endforeach

                        </div>
                    </div>

                </div>
                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
            </div>
        </div>

        @component('emails.components.spacer', ['color' => '#ffffff'])
        @endcomponent

    @endif

    <div style="mso-line-height-rule: exactly;line-height: 1px;font-size: 1px;">&nbsp;</div>

    @component('emails.components.spacer', ['color' => '#ffffff'])
    @endcomponent

    @foreach ($courses as $index => $course)
        <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
            <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
                <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
                <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                    <div style="Margin-left: 46px;Margin-right: 46px;">
                        <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                            <h2 class="size-18" style="Margin-top: 0;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #2f353e;font-size: 17px;line-height: 26px;" lang="x-size-18"><span style="color:#000000">{{ $course->getTitle($language) }} ({{ $course->getLanguage() }})</span></h2>
                        </div>
                    </div>

                </div>
                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
            </div>
        </div>

        <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
            <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
                <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
                <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                    <div style="Margin-left: 46px;Margin-right: 46px;">
                        <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                            <p></p>
                            <table style="width: 100%; margin-bottom: 10px;">
                                @if (! empty($course->location))
                                    <tr class="size-15" style="Margin-top: 0;Margin-bottom: 10px;font-size: 15px;line-height: 23px;" lang="x-size-15">
                                        <td style="color:#8e8e8e; width: 20% ">{{ $language === 'da' ? 'Lokation' : 'Location' }}:&nbsp;</td>
                                        <td style="color:#000000; Margin-bottom: 10px;">{{ $course->location->getDisplayString() }}</td>
                                    </tr>
                                @endif
                            </table>
                            <table style="width: 100%">
                                @foreach ($course->getCourseDatesFormatted($order->language) as $index => $date)
                                    <tr class="size-15" style="Margin-top: 20px;Margin-bottom: 0;font-size: 15px;line-height: 23px;" lang="x-size-15">
                                        <td style="color:#8e8e8e; width: 20%">{{ $language === 'da' ? 'Dag' : 'Day' }} {{ $index + 1 }}:</td>
                                        <td style="color:#000000">{{ $date }}</td>
                                        @if (! empty($course->getCourseTimes()[$index]))
                                            <td style="color:#000000">
                                                {{ $course->getCourseTimes()[$index] }}
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>

                </div>
                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
            </div>
        </div>

        @component('emails.components.spacer', ['color' => '#ffffff'])
        @endcomponent
    @endforeach

    <div style="mso-line-height-rule: exactly;line-height: 1px;font-size: 1px;">&nbsp;</div>

    @component('emails.components.spacer', ['color' => '#ffffff'])
    @endcomponent

    <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
        <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
            <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
            <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                <div style="Margin-left: 46px;Margin-right: 46px;">
                    <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                        <p class="size-22" style="Margin-top: 0;Margin-bottom: 0;font-size: 18px;line-height: 26px;" lang="x-size-22">
                            @if ($language === 'da')
                                <span style="color:#8e8e8e; margin-right: 20px;">Samlet pris:</span> <span style="">DKK {{ number_format($order->getTotalPrice(), 0, ',', '.') }},- eks. moms</span>
                            @else
                                <span style="color:#8e8e8e; margin-right: 20px;">Total price:</span> <span style="">DKK {{ number_format($order->getTotalPrice(), 0, ',', '.') }},- ex. vat</span>
                            @endif
                        </p>
                    </div>
                </div>

            </div>
            <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
        </div>
    </div>

    @component('emails.components.spacer', ['color' => '#ffffff'])
    @endcomponent
@endsection

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
        <a style="text-decoration: underline;transition: opacity 0.1s ease-in;color: #000;" href="{{ $course->getLink() }}">{{ $course->getTitle($language) }} ({{ $course->getLanguage() }})</a>
        @if (! $loop->last && count($courses) > 1)
            <br>
        @endif
    @endforeach
@endsection
