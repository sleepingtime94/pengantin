@extends('layouts.app')

@section('title', 'PENGANTIN')

@section('content')
<div class="box-center">
    <div class="card border-0 rounded-lg mx-auto" style="max-width: 25rem;">
        <div class="card-body p-4 p-sm-5">
            <div class="text-center mb-4">
                <img src="/assets/img/logo2.png" alt="DISDUKCAPIL TAPIN" class="img-fluid mb-3" style="max-width: 250px;">
                <h4 class="fw-bold">Selamat Datang!</h4>
                <p class="text-muted">Silahkan login untuk melanjutkan.</p>
            </div>

            <!-- Input NIK -->
            <div class="form-floating mb-3">
                <input id="nik" type="text" class="form-control form-control-lg" placeholder="NIK " aria-label="NIK " maxlength="16" required>
                <label for="nik"><i class="bi bi-person-vcard me-2"></i>NIK</label>
            </div>

            <!-- Input Kata Sandi -->
            <div class="input-group mb-3">
                <div class="form-floating flex-grow-1">
                    <input id="pass" type="password" class="form-control form-control-lg" placeholder="Kata Sandi" aria-label="Kata Sandi" />
                    <label for="pass"><i class="bi bi-lock me-2"></i>Kata Sandi</label>
                </div>
                <button id="show-pass" class="btn btn-outline-secondary" type="button" style="height: 3.625rem; line-height: 2.5rem;">
                    <i class="bi bi-eye-fill"></i>
                </button>
            </div>

            <input id="csrf-token" type="hidden" name="csrf_token" value="{{ $csrf_token }}">
            <div class="cf-turnstile" data-size="flexible" data-sitekey="0x4AAAAAABUKLiFLTYnJ3ykO"></div>
            <div class="d-grid gap-2 my-2">
                <button id="login" class="btn btn-primary btn-lg">Masuk</button>
                <button onclick="window.location.href='/register'" class="btn btn-success btn-lg">Registrasi</button>
            </div>
            <div class="text-center mt-3">
                <p><button class="btn btn-link text-decoration-none">Reset Kata Sandi</button></p>
            </div>
        </div>
    </div>
</div>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
<script>
    $(document).ready(function() {
        const $passwordInput = $('#pass');
        const $showPassButton = $('#show-pass');

        $showPassButton.on('click', function() {
            const type = $passwordInput.attr('type') === 'password' ? 'text' : 'password';
            $passwordInput.attr('type', type);

            const $icon = $showPassButton.find('i');
            if (type === 'password') {
                $icon.removeClass('bi-eye-slash-fill').addClass('bi-eye-fill');
            } else {
                $icon.removeClass('bi-eye-fill').addClass('bi-eye-slash-fill');
            }
        });

        $('#nik').on('input', function() {
            var nik = $(this).val();
            if (/^\d*$/.test(nik)) {
                if (nik.length === 16) {
                    $('#nik').removeClass('is-invalid');
                    $('#nik').addClass('is-valid');
                } else {
                    $('#nik').removeClass('is-valid');
                    $('#nik').addClass('is-invalid');
                }
            } else {
                $('#nik').removeClass('is-valid');
                $('#nik').addClass('is-invalid');
            }
        });

        $('#login').on('click', function() {
            var nik = $('#nik').val();
            var pass = $('#pass').val();
            var csrfToken = $('#csrf-token').val();
            var formData = {
                nik: nik
                , pass: pass
                , csrf_token: csrfToken
            , };

            $.ajax({
                url: '/user/login'
                , type: 'POST'
                , data: formData
                , success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success'
                            , title: 'Berhasil'
                            , text: response.message
                            , showConfirmButton: false
                            , allowOutsideClick: false
                            , timer: 1500
                        }).then(function() {
                            window.location.href = '/dashboard';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error'
                            , title: 'Gagal'
                            , text: response.message
                            , showConfirmButton: false
                            , timer: 1500
                        });
                    }
                }
                , error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error'
                        , title: 'Gagal'
                        , text: 'Terjadi kesalahan saat mengirim permintaan.'
                        , showConfirmButton: false
                        , timer: 1500
                    });
                }
            });
        })
    });

</script>
@endsection
