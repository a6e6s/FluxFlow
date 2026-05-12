@php($dir = app()->getLocale() === 'ar' ? 'rtl' : 'ltr')
<!DOCTYPE html>
<html dir="{{ $dir }}" lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('invitations.mail.subject', ['task' => $task?->title ?? '']) }}</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f7fafc; padding:24px; color:#1a202c;">
    <div style="max-width:560px; margin:0 auto; background:#ffffff; border-radius:12px; padding:32px; box-shadow:0 1px 3px rgba(0,0,0,0.08);">
        <h1 style="font-size:20px; margin:0 0 16px;">{{ __('invitations.mail.greeting') }}</h1>

        <p style="line-height:1.6;">
            {{ __('invitations.mail.line1', [
                'inviter' => $inviter?->name ?? '',
                'task' => $task?->title ?? '',
                'project' => $project?->title ?? '',
            ]) }}
        </p>

        <p style="line-height:1.6;">{{ __('invitations.mail.line2') }}</p>

        <p style="text-align:center; margin:32px 0;">
            <a href="{{ $acceptUrl }}"
                style="background:#1392ec; color:#ffffff; padding:12px 24px; border-radius:8px; text-decoration:none; display:inline-block; font-weight:600;">
                {{ __('invitations.mail.cta') }}
            </a>
        </p>

        <p style="font-size:12px; color:#718096; line-height:1.5;">
            {{ __('invitations.mail.fallback') }}<br>
            <a href="{{ $acceptUrl }}" style="color:#1392ec;">{{ $acceptUrl }}</a>
        </p>
    </div>
</body>
</html>
