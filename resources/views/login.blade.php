<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #ffffff;
            font-family: "Hiragino Sans", "Hiragino Kaku Gothic ProN", "Yu Gothic", Meiryo, sans-serif;
            color: #222222;
        }

        /* Main wrapper to center contents horizontally and vertically */
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 580px;
        }

        /* Microsoft 365 Red Pill Button */
        .btn-ms365 {
            background-color: #cc0909;
            color: #ffffff;
            font-weight: 700;
            font-size: 16px;
            border-radius: 50px;
            height: 50px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            width: 100%;
            max-width: 430px;
            border: none;
            transition: background-color 0.2s ease;
        }

        .btn-ms365:hover, .btn-ms365:focus {
            background-color: #a80707;
            color: #ffffff;
        }

        /* Form Row: Label right adjacent to input box */
        .form-row-custom {
            display: flex;
            align-items: center;
            margin-bottom: 16px;
        }

        .form-label-custom {
            width: 210px;
            text-align: right;
            padding-right: 14px;
            font-weight: 700;
            font-size: 14px;
            color: #333333;
            margin-bottom: 0;
            flex-shrink: 0;
        }

        .form-input-custom {
            flex: 1;
            height: 42px;
            border: 1px solid #c9c9c9;
            border-radius: 6px;
            padding: 0 12px;
            font-size: 14px;
            background-color: #ffffff;
        }

        .form-input-custom:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 1px #2563eb;
            outline: none;
        }

        /* Alignment row for checkbox and login button under input column */
        .form-offset-custom {
            display: flex;
        }

        .form-offset-space {
            width: 210px;
            flex-shrink: 0;
        }

        .form-offset-content {
            flex: 1;
        }

        /* Checkbox Style */
        .show-pass-label {
            font-size: 14px;
            color: #333333;
            cursor: pointer;
            user-select: none;
            display: inline-flex;
            align-items: center;
        }

        .show-pass-checkbox {
            width: 16px;
            height: 16px;
            margin-right: 8px;
            cursor: pointer;
        }

        /* Navy Blue Login Button */
        .btn-login {
            background-color: #1b365d;
            color: #ffffff;
            font-weight: 700;
            font-size: 15px;
            border-radius: 50px;
            height: 44px;
            min-width: 140px;
            padding: 0 32px;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s ease;
        }

        .btn-login:hover, .btn-login:focus {
            background-color: #122543;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">

            <!-- Microsoft 365 Red Pill Button -->
            <div class="text-center mb-4 pb-2">
                <a href="{{ route('ms-login') }}" class="btn btn-ms365 shadow-sm">
                    Microsoft365アカウントでログイン
                </a>
            </div>

            <!-- Main Login Form -->
            <form action="#" method="POST">
                @csrf

                <!-- User ID Row -->
                <div class="form-row-custom">
                    <label for="username" class="form-label-custom">
                        ユーザID(メールアドレス)
                    </label>
                    <input 
                        type="email" 
                        id="username" 
                        name="username" 
                        value="{{ old('username') }}" 
                        autocomplete="username" 
                        required 
                        class="form-control form-input-custom"
                    >
                </div>
                @error('username')
                    <div class="form-offset-custom mb-2">
                        <div class="form-offset-space"></div>
                        <div class="form-offset-content text-danger small">{{ $message }}</div>
                    </div>
                @enderror

                <!-- Password Row -->
                <div class="form-row-custom">
                    <label for="password" class="form-label-custom">
                        パスワード
                    </label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        autocomplete="current-password" 
                        required 
                        class="form-control form-input-custom"
                    >
                </div>
                @error('password')
                    <div class="form-offset-custom mb-2">
                        <div class="form-offset-space"></div>
                        <div class="form-offset-content text-danger small">{{ $message }}</div>
                    </div>
                @enderror

                <!-- Show Password Checkbox -->
                <div class="form-offset-custom mt-2">
                    <div class="form-offset-space"></div>
                    <div class="form-offset-content">
                        <label for="toggle-password" class="show-pass-label">
                            <input type="checkbox" id="toggle-password" class="form-check-input show-pass-checkbox mt-0">
                            <span>パスワードを表示する</span>
                        </label>
                    </div>
                </div>

                <!-- Submit Button (Left-aligned with checkbox & input column) -->
                <div class="form-offset-custom mt-4 pt-2">
                    <div class="form-offset-space"></div>
                    <div class="form-offset-content">
                        <button type="submit" class="btn btn-login shadow-sm">
                            ログイン
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const togglePassword = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('change', function () {
                passwordInput.type = this.checked ? 'text' : 'password';
            });
        }
    </script>
</body>
</html>