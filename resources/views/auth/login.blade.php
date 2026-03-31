<x-guest-layout>
    <style>
        .login-shell {
            min-height: 100dvh;
            display: grid;
            grid-template-columns: 1fr;
            align-items: center;
            gap: 20px;
            color: #e2e8f0;
            background: radial-gradient(circle at 20% 10%, rgba(6, 182, 212, 0.18), transparent 30%),
                radial-gradient(circle at 80% 20%, rgba(99, 102, 241, 0.2), transparent 35%),
                #020617;
            width: min(1240px, 100%);
            margin: 0 auto;
            padding: clamp(20px, 3vw, 34px);
            box-sizing: border-box;
        }

        .login-shell * {
            box-sizing: border-box;
        }

        .login-left,
        .login-right {
            width: 100%;
            padding: clamp(12px, 1.8vw, 24px);
        }

        .kicker {
            display: inline-block;
            font: 600 11px/1.2 "Segoe UI", Arial, sans-serif;
            letter-spacing: .2em;
            color: #67e8f9;
            border: 1px solid rgba(255, 255, 255, .18);
            border-radius: 999px;
            padding: 8px 12px;
            margin-bottom: 16px;
        }

        .brand-title {
            margin: 0 0 12px;
            font: 700 clamp(30px, 4.5vw, 44px)/1.06 "Segoe UI", Arial, sans-serif;
            color: #fff;
            overflow-wrap: anywhere;
            letter-spacing: -.02em;
        }

        .brand-copy {
            margin: 0;
            color: #cbd5e1;
            max-width: 58ch;
            font: 400 clamp(15px, 1.6vw, 17px)/1.6 "Segoe UI", Arial, sans-serif;
        }

        .facts {
            margin-top: 24px;
            display: grid;
            gap: 12px;
        }

        .fact {
            border: 1px solid rgba(255, 255, 255, .15);
            background: rgba(15, 23, 42, .55);
            border-radius: 14px;
            padding: 14px;
            min-height: 84px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .fact .label {
            font: 400 13px/1.2 "Segoe UI", Arial, sans-serif;
            color: #94a3b8;
        }

        .fact .value {
            margin-top: 5px;
            font: 600 15px/1.3 "Segoe UI", Arial, sans-serif;
            color: #fff;
        }

        .login-card {
            width: 100%;
            max-width: 470px;
            margin: 0 auto;
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 20px;
            background: rgba(15, 23, 42, .85);
            box-shadow: 0 18px 44px rgba(2, 6, 23, .52);
            backdrop-filter: blur(2px);
            padding: clamp(20px, 3vw, 30px);
        }

        .login-card h2 {
            margin: 0;
            font: 700 clamp(26px, 3vw, 30px)/1.1 "Segoe UI", Arial, sans-serif;
            color: #fff;
            letter-spacing: -.015em;
        }

        .login-subtitle {
            margin: 10px 0 24px;
            color: #94a3b8;
            font: 400 14px/1.5 "Segoe UI", Arial, sans-serif;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #e2e8f0;
            font: 600 14px/1.3 "Segoe UI", Arial, sans-serif;
        }

        .form-input {
            width: 100%;
            height: 48px;
            border-radius: 12px;
            border: 1px solid #475569;
            background: rgba(15, 23, 42, .9);
            color: #fff;
            padding: 0 14px;
            font: 400 15px/1.2 "Segoe UI", Arial, sans-serif;
            transition: border-color .18s ease, box-shadow .18s ease, background-color .18s ease;
        }

        .form-input::placeholder {
            color: #94a3b8;
        }

        .form-input:focus {
            outline: none;
            border-color: #22d3ee;
            box-shadow: 0 0 0 3px rgba(34, 211, 238, .2);
        }

        .form-row {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin: 2px 0 18px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #cbd5e1;
            font: 400 14px/1.3 "Segoe UI", Arial, sans-serif;
        }

        .remember input[type="checkbox"] {
            width: 16px;
            height: 16px;
            margin: 0;
            accent-color: #22d3ee;
            cursor: pointer;
        }

        .reset-link {
            color: #67e8f9;
            text-decoration: none;
            font: 500 14px/1.2 "Segoe UI", Arial, sans-serif;
        }

        .reset-link:hover {
            color: #a5f3fc;
        }

        .submit-btn {
            width: 100%;
            height: 48px;
            border: 0;
            border-radius: 12px;
            color: #fff;
            font: 700 15px/1 "Segoe UI", Arial, sans-serif;
            cursor: pointer;
            background: linear-gradient(90deg, #06b6d4 0%, #4f46e5 100%);
            transition: transform .15s ease, filter .15s ease, box-shadow .15s ease;
        }

        .submit-btn:hover {
            filter: brightness(1.05);
            box-shadow: 0 10px 24px rgba(79, 70, 229, .35);
        }

        .submit-btn:active {
            transform: translateY(1px);
        }

        .secure-note {
            margin-top: 16px;
            color: #94a3b8;
            font: 400 12px/1.4 "Segoe UI", Arial, sans-serif;
            text-align: center;
        }

        .error-text {
            margin-top: 6px;
            color: #fecaca;
            font-size: 13px;
        }

        @media (min-width: 992px) {
            .login-shell {
                grid-template-columns: minmax(0, 1.15fr) minmax(380px, .85fr);
                align-items: center;
                gap: clamp(26px, 3.2vw, 48px);
            }

            .login-left,
            .login-right {
                padding: clamp(22px, 2.2vw, 36px);
            }

            .login-left {
                max-width: 700px;
                margin: 0 auto;
            }

            .facts {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .form-row {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        @media (max-width: 991px) {
            .login-shell {
                padding-block: 20px 24px;
            }

            .login-left {
                order: 1;
            }

            .login-right {
                order: 2;
            }
        }

        @media (max-width: 640px) {
            .login-shell {
                gap: 14px;
                padding: 14px 12px 20px;
            }

            .login-left,
            .login-right {
                padding: 8px;
            }

            .login-card {
                border-radius: 16px;
            }
        }
    </style>

    <section class="login-shell">
        <div class="login-left">
            <p class="kicker">PLATAFORMA FINANCEIRA</p>
            <h1 class="brand-title">Teles Empréstimos</h1>
            <p class="brand-copy">Gestão financeira com segurança, precisão operacional e visão estratégica para decisões de crédito.</p>

            <div class="facts">
                <div class="fact">
                    <div class="label">Ambiente</div>
                    <div class="value">Operação protegida</div>
                </div>
                <div class="fact">
                    <div class="label">Confiabilidade</div>
                    <div class="value">Infraestrutura estável</div>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-card">
                <h2>Acesso ao painel</h2>
                <p class="login-subtitle">Entre com suas credenciais para continuar.</p>

                <x-auth-session-status class="error-text" :status="session('status')" />

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email" class="form-label">E-mail</label>
                        <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="seuemail@empresa.com">
                        <x-input-error :messages="$errors->get('email')" class="error-text" />
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Senha</label>
                        <input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" placeholder="Digite sua senha">
                        <x-input-error :messages="$errors->get('password')" class="error-text" />
                    </div>

                    <div class="form-row">
                        <label for="remember_me" class="remember">
                            <input id="remember_me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                            <span>Lembrar-me</span>
                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="reset-link">Esqueci minha senha</a>
                        @endif
                    </div>

                    <button type="submit" class="submit-btn">Entrar</button>
                </form>

                <p class="secure-note">Ambiente seguro e monitorado</p>
            </div>
        </div>
    </section>
</x-guest-layout>
