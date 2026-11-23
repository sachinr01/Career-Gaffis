@component('mail::message')
{{-- Greeting --}}
@if (! empty($greeting))
<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px;  color: #333;">{{ $greeting }}</p>
@else
@if ($level == 'error')
<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px;  color: #333;">@lang('Whoops!')</p>
@else
{{--<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px;  color: #333;">@lang('Hello!')</p> --}}
@endif
@endif

{{-- Intro Lines --}}
@foreach ($introLines as $line)
<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">{!! $line !!}</p>
@endforeach

{{-- Action Button --}}
@isset($actionText)
<?php
    switch ($level) {
        case 'success':
            $color = 'green';
            break;
        case 'error':
            $color = 'red';
            break;
        default:
            $color = 'blue';
    }
?>
@component('mail::button', ['url' => $actionUrl, 'color' => $color])


 <span style="font-family: Roboto, Arial, sans-serif; font-size: 16px;">{!! $actionText !!}</span>
@endcomponent
@endisset

{{-- Outro Lines --}}
@foreach ($outroLines as $line)
<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">{!! $line !!}</p>
@endforeach

{{-- Salutation --}}
@if (! empty($salutation))
<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">{!! $salutation !!}</p>
@else
<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">
    @lang('Best Regards'),<br/>
    HR Team,<br/>
    Gaffis Technologies Private Limited.
</p>
@endif

{{-- Social Media Icons --}}
<p style="margin: 20px 0;">
    <a href="https://www.facebook.com/gaffistech" target="_blank" style="margin-right: 8px;">
        <img src="https://careers.gaffis.com/public/social_media_icons/facebook-new.png" alt="Facebook" width="28" height="28" style="vertical-align: middle;">
    </a>

    <a href="https://www.linkedin.com/company/gaffis-technologies" target="_blank" style="margin-right: 8px;">
        <img src="https://careers.gaffis.com/public/social_media_icons/linkedin.png" alt="LinkedIn" width="28" height="28" style="vertical-align: middle;">
    </a>

    <a href="https://whatsapp.com/channel/0029Va5ZEqBIHphCQMtvB82c" target="_blank" style="margin-right: 8px;">
        <img src="https://careers.gaffis.com/public/social_media_icons/whatsapp.png" alt="WhatsApp" width="28" height="28" style="vertical-align: middle;">
    </a>

    <a href="https://www.google.com/search?q=Gaffis+Technologies+Pvt.+Ltd" target="_blank" style="margin-right: 8px;">
        <img src="https://careers.gaffis.com/public/social_media_icons/google-logo.png" alt="Google" width="28" height="28" style="vertical-align: middle;">
    </a>

    <a href="https://www.youtube.com/@gaffistech" target="_blank">
        <img src="https://careers.gaffis.com/public/social_media_icons/youtube-play.png" alt="YouTube" width="28" height="28" style="vertical-align: middle;">
    </a>
</p>


{{-- Subcopy --}}
@isset($actionText)
@component('mail::subcopy')
@lang(
    "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
    'into your web browser: [:actionURL](:actionURL)',
    [
        'actionText' => $actionText,
        'actionURL' => $actionUrl
    ]
)
@endcomponent
@endisset
@endcomponent
