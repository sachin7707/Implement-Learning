@if (! empty($participants))
    <div class="layout one-col fixed-width" style="Margin: 0 auto;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);overflow-wrap: break-word;word-wrap: break-word;word-break: break-word;">
        <div class="layout__inner" style="border-collapse: collapse;display: table;width: 100%;background-color: #ffffff;">
            <!--[if (mso)|(IE)]><table align="center" cellpadding="0" cellspacing="0" role="presentation"><tr class="layout-fixed-width" style="background-color: #ffffff;"><td style="width: 600px" class="w560"><![endif]-->
            <div class="column" style="text-align: left;color: #2f353e;font-size: 15px;line-height: 23px;font-family: Calibri,Carlito,PT Sans,Trebuchet MS,sans-serif;max-width: 600px;min-width: 320px; width: 320px;width: calc(28000% - 167400px);">

                <div style="Margin-left: 46px;Margin-right: 46px;">
                    <div style="mso-line-height-rule: exactly;mso-text-raise: 4px;">
                        <p></p>
                        @foreach ($participants as $participant)
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