@component('mail::message')
# {{ $content['title'] }}

Dear {{ $content['fullname'] }},

Please find below link to access your recent test results from GTI-HC.

@component('mail::button', ['url' => $content['url']])
Download Results
@endcomponent

Please contact us for further clarifications or any issues. <br>

+233(0)55 333 9969 | https://www.gtihc.com

Thanks,<br>
{{ config('app.name') }}
@endcomponent
