@extends('layouts.email', [
    'courses' => $order->courses,
    'footer' => json_decode($footer->text),
    'language' => $language,
    'defaultBody' => $defaultBody,
    'intro' => str_replace('$name', $participant->name, $intro->text),
])
@section('title', $language === 'da' ? 'DELTAGER EMAIL' : 'PARTICIPANT EMAIL')

@section('emailcontent')
    @foreach ($courses as $course)
        <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
            <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
                <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
                <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                    <div style="Margin-left: 46px;Margin-right: 46px;">
                        <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                            <h2 class="size-18" style="Margin-top: 0;Margin-bottom: 16px;font-style: normal;font-weight: normal;color: #2f353e;font-size: 17px;line-height: 26px;" lang="x-size-18"><span style="color:#000000">{{ $course->getTitle($language) }} ({{ $course->getLanguage() }})</span></h2>
                        </div>
                    </div>

                    <div style="Margin-left: 46px;Margin-right: 46px;">
                        <div style="mso-line-height-rule: exactly;line-height: 1px;font-size: 1px;">&nbsp;</div>
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
                            <table style="width: 100%; margin-bottom: 10px;">
                                @if (! empty($course->location))
                                    <tr class="size-15" style="Margin-top: 0;Margin-bottom: 10px;font-size: 15px;line-height: 23px;" lang="x-size-15">
                                        <td style="color:#8e8e8e; width: 20% ">{{ $language === 'da' ? 'Lokation' : 'Location' }}&nbsp;</td>
                                        <td style="color:#000000; Margin-bottom: 10px;">{{ $course->location->getDisplayString() }}</td>
                                    </tr>
                                @endif
                            </table>
                            <table style="width: 100%">
                                @foreach ($course->getCourseDatesFormatted($order->language) as $index => $date)
                                    <tr class="size-15" style="Margin-top: 20px;Margin-bottom: 0;font-size: 15px;line-height: 23px;" lang="x-size-15">
                                        <td style="color:#8e8e8e; width: 20%">{{ $language === 'da' ? 'Dag' : 'Day' }} {{ $index + 1 }}:</td>
                                        <td style="color:#000000">
                                            {{ $date }}
                                        </td>
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


        @if ($course->hasText('before_course'))
            <!-- shows a text, about what to do, before attending the course -->
            <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
                <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
                    <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
                    <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                        <div style="Margin-left: 46px;Margin-right: 46px;">
                            <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                                <p class="size-15" style="Margin-top: 0;Margin-bottom: 0;font-size: 15px;line-height: 23px;" lang="x-size-15">
                                    <span style="color:#000000">{{ $beforeCourseHeader }}</span>
                                </p>
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
                            <div style="mso-line-height-rule: exactly;line-height: 20px;font-size: 1px;">&nbsp;</div>
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
                                <p class="size-15" style="Margin-top: 0;Margin-bottom: 0;font-size: 15px;line-height: 23px;" lang="x-size-15"><span style="color:#000000">{{ $course->getText('before_course') }}</span></p>
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
                            <div style="mso-line-height-rule: exactly;line-height: 40px;font-size: 1px;">&nbsp;</div>
                        </div>

                    </div>
                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                </div>
            </div>
        @endif

        <!-- TODO: handle course link -->
        <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
            <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
                <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
                <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                    <div style="Margin-left: 46px;Margin-right: 46px;">
                        <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                            <p class="size-18" style="Margin-top: 0;Margin-bottom: 0;font-size: 17px;line-height: 26px;" lang="x-size-18"><span style="color:#000000"><a style="text-decoration: underline;transition: opacity 0.1s ease-in;color: #000;" href="{{ $course->getLink() }}">{{ $language === 'da' ? 'Gå til side om kurset' : 'Go to page about the course' }} <img style="Margin-left: 20px;border: 0;display: inline-block;height: 17px;width: auto;" alt="" width="auto" src="{{ env('WEBSITE_URL') }}{{ env('MAIL_ARROW_IMAGE') }}"></a>&nbsp;</span></p>
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
                        <div style="mso-line-height-rule: exactly;line-height: 40px;font-size: 1px;">&nbsp;</div>
                    </div>

                </div>
                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
            </div>
        </div>

        <!-- only show this, when more than one course -->
        @if (count($courses) > 1 && ! $loop->last)
            <div style="mso-line-height-rule: exactly;line-height: 1px;font-size: 1px;">&nbsp;</div>

            <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
                <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
                    <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
                    <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                        <div style="Margin-left: 46px;Margin-right: 46px;">
                            <div style="mso-line-height-rule: exactly;line-height: 40px;font-size: 1px;">&nbsp;</div>
                        </div>

                    </div>
                    <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
                </div>
            </div>
        @endif
    @endforeach

    <!-- participants section end -->
@endsection

<!--<div>Calendar link: <a href="{{$calendarUrl}}">{{$calendarUrl}}</a></div>-->
