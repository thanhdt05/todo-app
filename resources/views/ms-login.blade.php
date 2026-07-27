<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in to your account</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f2f4f7;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(220, 230, 245, 0.6) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(235, 240, 250, 0.8) 0%, transparent 50%);
            font-family: "Segoe UI", -apple-system, BlinkMacSystemFont, Roboto, "Helvetica Neue", sans-serif;
            color: #1b1b1b;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .ms-wrapper {
            width: 100%;
            max-width: 440px;
        }

        /* Main Sign in Box */
        .ms-card {
            background-color: #ffffff;
            border-radius: 2px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            padding: 44px 44px 36px 44px;
        }

        /* Company Logo Header */
        .company-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
        }

        .company-logo-text {
            font-size: 17px;
            font-weight: 700;
            color: #103467;
            letter-spacing: -0.2px;
        }

        .ms-heading {
            font-size: 24px;
            font-weight: 600;
            color: #1b1b1b;
            margin-bottom: 20px;
        }

        /* Microsoft Style Underline Input */
        .ms-input {
            width: 100%;
            border: none;
            border-bottom: 1px solid #0067b8;
            border-radius: 0;
            padding: 6px 0;
            font-size: 15px;
            color: #1b1b1b;
            background: transparent;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .ms-input::placeholder {
            color: #707070;
        }

        .ms-input:focus {
            border-bottom: 2px solid #0067b8;
            box-shadow: none;
        }

        /* Help link */
        .ms-link {
            color: #0067b8;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            margin-top: 14px;
        }

        .ms-link:hover {
            text-decoration: underline;
            color: #004e8c;
        }

        /* Action Next Button */
        .ms-btn-primary {
            background-color: #0067b8;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            padding: 6px 36px;
            border: none;
            border-radius: 0px;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .ms-btn-primary:hover, .ms-btn-primary:focus {
            background-color: #005a9e;
            color: #ffffff;
        }

        /* Bottom Sign-in options card */
        .ms-options-card {
            background-color: #ffffff;
            border-radius: 2px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            margin-top: 16px;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background-color 0.2s ease;
            text-decoration: none;
        }

        .ms-options-card:hover {
            background-color: #f8f9fa;
        }

        .ms-options-text {
            font-size: 14px;
            color: #1b1b1b;
            margin-left: 12px;
        }
    </style>
</head>
<body>

    <div class="ms-wrapper">
        <!-- Main Sign in Card -->
        <div class="ms-card">
            <!-- Company Logo -->
            <div class="company-logo">
                <svg width="28" height="28" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M4 8C4 8 12 4 20 8C28 12 28 20 20 24C12 28 4 24 4 24" stroke="#103467" stroke-width="3" stroke-linecap="round"/>
                    <path d="M8 12L24 12" stroke="#103467" stroke-width="2.5"/>
                    <path d="M6 16L26 16" stroke="#103467" stroke-width="2.5"/>
                    <path d="M8 20L24 20" stroke="#103467" stroke-width="2.5"/>
                </svg>
                <span class="company-logo-text">日本ソフトウェア株式会社</span>
            </div>

            <!-- Heading -->
            <h1 class="ms-heading">Sign in</h1>

            <!-- Form -->
            <form action="#" method="POST">
                @csrf
                <div class="mb-2">
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="ms-input" 
                        placeholder="user@nsware.co.jp"
                        required
                        autofocus
                    >
                </div>

                <div>
                    <a href="#" class="ms-link">Can't access your account?</a>
                </div>

                <!-- Next Button Right Aligned -->
                <div class="d-flex justify-content-end mt-4 pt-3">
                    <button type="submit" class="ms-btn-primary">Next</button>
                </div>
            </form>
        </div>

        <!-- Sign-in options Bottom Card -->
        <a href="#" class="ms-options-card">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#505050" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="7.5" cy="15.5" r="5.5"></circle>
                <path d="M11.5 11.5L20 3"></path>
                <path d="M16 7L18.5 4.5"></path>
                <path d="M18 9L21 6"></path>
            </svg>
            <span class="ms-options-text">Sign-in options</span>
        </a>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
