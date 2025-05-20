@component('mail::message')
# Reset Password

Klik tombol di bawah ini untuk mengatur ulang password akun Anda.

@component('mail::button', ['url' => $resetLink])
Reset Password
@endcomponent

Link ini akan kadaluarsa dalam 60 menit.

Jika Anda tidak meminta reset password, silakan abaikan email ini.

Terima kasih,<br>
{{ config('app.name') }}
@endcomponent