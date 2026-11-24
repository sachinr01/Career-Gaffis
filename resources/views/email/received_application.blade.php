@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ $jobApplication->job?->title }}
        @endcomponent
    @endslot
    {{-- Body --}}
    {{-- @lang('email.hello') {{ $jobApplication->full_name }}! --}}
    <p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">
       @lang('email.hello') {{ $jobApplication->full_name }}!
    </p>
    <p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">
        @lang('email.applicationReceived.thankYouForApplying', ['jobTitle' => $jobApplication->job?->title])
    </p>

    <p style="font-family: Roboto, Arial, sans-serif; font-size: 16px; color: #333;">
        {!! __('email.applicationReceived.receivedApplication') !!}
    </p>


    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset
    
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

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} Gaffis Technologies Private Limited. @lang('All rights reserved.')
        @endcomponent
    @endslot
@endcomponent
