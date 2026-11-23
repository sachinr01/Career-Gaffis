@component('mail::message')
{{-- Greeting --}}
<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">
    #{!! $greeting !!}
</p>

<p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">
    {!! __('email.newJobApplication.subject') !!}
</p>

@component('mail::text', ['text' => $content])
@endcomponent

@if($buttonUrl)
    @component('mail::extrabutton', ['buttonurl' => $buttonUrl])
        <span style="font-family: Roboto, Arial, sans-serif; font-size: 16px;">{!! $extraButtonText !!}</span>
    @endcomponent
@endif

@component('mail::button', ['url' => $url])
    <span style="font-family: Roboto, Arial, sans-serif; font-size: 16px;">{!! $buttonText !!}</span>
@endcomponent

{{-- Salutation --}}
@if (! empty($salutation))
    <p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">
        {!! $salutation !!}
    </p>
@else
    <p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">
        @lang('Best Regards'),<br/>
        HR Team,<br/>
        Gaffis Technologies Private Limited.
    </p>
    
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

@endif

{{-- Subcopy --}}
@isset($url)
    @slot('subcopy')
        <p style="font-family: Roboto, Arial, sans-serif; font-size: 13px; color: #555;">
            @lang(
                "If you’re having trouble clicking the \":actionText\" button, copy and paste the URL below\n".
                'into your web browser: [:actionURL](:actionURL)',
                [
                    'actionText' => $buttonText,
                    'actionURL' => $url
                ]
            )
        </p>
    @endslot
@endisset

{{-- Footer --}}
@slot('footer')
    @component('mail::footer')
        <span style="font-family: Roboto, Arial, sans-serif; font-size: 12px; color: #999;">
            © {{ date('Y') }} Gaffis Technologies Private Limited. @lang('All rights reserved.')
        </span>
    @endcomponent
@endslot
@endcomponent
