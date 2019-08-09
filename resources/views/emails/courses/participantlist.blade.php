@extends('layouts.email', [
    'course' => $course,
    'footer' => json_decode($footer->text),
    'default_body' => $defaultBody,
])
@section('title', $language === 'da' ? 'Deltagerliste' : 'Participant list')

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

    @component('emails.components.participants', ['participants' => $participants, 'language' => $language])
    @endcomponent

    @component('emails.components.spacer', ['color' => '#ffffff'])
    @endcomponent

    @if (! empty($participantsOnWaitingList))

        <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
            <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
                <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
                <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                    <div style="Margin-left: 46px;Margin-right: 46px;">
                        <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                            <h2 class="size-18" style="Margin-top: 0;Margin-bottom: 0;font-style: normal;font-weight: normal;color: #2f353e;font-size: 17px;line-height: 26px;" lang="x-size-18">{{ $language === 'da' ? 'Deltagere på venteliste' : 'Participants on waiting list' }}</h2>
                        </div>
                    </div>

                </div>
                <!--[if (mso)|(IE)]></td></tr></table><![endif]-->
            </div>
        </div>

        @component('emails.components.participants', ['participants' => $participantsOnWaitingList, 'language' => $language])
        @endcomponent

        @component('emails.components.spacer', ['color' => '#ffffff'])
        @endcomponent

    @endif


    @component('emails.components.course', ['course' => $course, 'language' => $language])
    @endcomponent

    @component('emails.components.spacer', ['color' => '#ffffff'])
    @endcomponent
@endsection
