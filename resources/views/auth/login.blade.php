<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Penggajian</title>
    {{-- Memuat Font Awesome untuk ikon --}}
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f8f8ff;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            color: #1f2937;
        }

        .login-wrapper {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-wrapper h2 {
            margin: 0 0 1rem 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .login-wrapper p {
            margin-bottom: 2rem;
            color: #6b7280;
            font-size: 0.875rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
            text-align: left;
        }

        .form-group .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .form-control {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn-submit {
            width: 100%;
            background-color: #1e40af;
            color: white;
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 0.375rem;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-submit:hover {
            background-color: #1e3a8a;
        }

        .btn-submit:hover {
            opacity: 0.9;
        }

        .btn-submit:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .error-message {
            color: #d9534f;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            padding: 10px;
            font-size: 14px;
            margin-top: 20px;
            display: none;
            /* Disembunyikan secara default */
        }

        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
            display: none;
            /* Pastikan spinner disembunyikan secara default */
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <h2><i class="fas fa-user-circle"></i> Selamat Datang Kembali</h2>
        <p>Silakan masukkan kredensial Anda untuk masuk.</p>

        <form id="loginForm" method="POST" action="{{ route('login.submit') }}">
            @csrf
            <div class="form-group">
                <i class="fas fa-envelope icon"></i>
                <input id="email" type="email" name="email" class="form-control" placeholder="Alamat Email"
                    required autofocus>
            </div>
            <div class="form-group">
                <i class="fas fa-lock icon"></i>
                <input id="password" type="password" name="password" class="form-control" placeholder="Password"
                    required>
            </div>

            <div id="errorMessage" class="error-message"></div>

            <button type="submit" class="btn-submit mt-4">
                <span class="spinner d-none"></span>
                <span class="btn-text">Login</span>
            </button>
        </form>
    </div>

    {{-- Memuat jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#loginForm').on('submit', function(event) {
                event.preventDefault();

                let form = $(this);
                let button = form.find('.btn-submit');
                let spinner = button.find('.spinner');
                let btnText = button.find('.btn-text');
                let errorMessage = $('#errorMessage');

                // Hapus pesan error lama dan tampilkan loading
                errorMessage.hide().text('');
                spinner.css('display', 'inline-block'); // Tampilkan spinner
                spinner.removeClass('d-none');
                btnText.text('Logging in...');
                button.attr('disabled', true);

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        window.location.href = response.redirect;
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            let errorMsg = xhr.responseJSON.message ||
                                'Terjadi kesalahan validasi.';
                            errorMessage.text(errorMsg).show();
                        } else {
                            errorMessage.text(
                                'Terjadi kesalahan pada server. Silakan coba lagi.').show();
                        }

                        // Kembalikan tombol ke keadaan semula
                        spinner.addClass('d-none');
                        btnText.text('Login');
                        button.attr('disabled', false);
                    }
                });
            });
        });
    </script>
</body>

</html>
