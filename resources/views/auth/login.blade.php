<x-guest-layout>
    <section class="relative min-h-screen overflow-hidden bg-slate-950 text-slate-100">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-28 -top-20 h-80 w-80 rounded-full bg-cyan-500/15 blur-3xl"></div>
            <div class="absolute right-0 top-1/4 h-96 w-96 rounded-full bg-indigo-500/15 blur-3xl"></div>
            <div class="absolute bottom-0 left-1/3 h-72 w-72 rounded-full bg-emerald-400/10 blur-3xl"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.08),transparent_40%)]"></div>
        </div>

        <div class="relative mx-auto grid min-h-screen max-w-7xl items-center px-6 py-8 lg:grid-cols-2 lg:gap-14 lg:px-10">
            <div class="hidden lg:block">
                <p class="mb-5 inline-flex items-center rounded-full border border-white/20 bg-white/5 px-4 py-1 text-xs tracking-[0.25em] text-cyan-200">
                    FINANCE PLATFORM
                </p>
                <h1 class="max-w-xl text-5xl font-semibold leading-tight text-white">
                    {{ config('app.name', 'TelesBank') }}
                </h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-slate-300">
                    Gestão financeira com segurança, precisão operacional e visão estratégica para decisões de crédito.
                </p>
                <div class="mt-10 grid max-w-xl grid-cols-2 gap-4 text-sm">
                    <div class="rounded-2xl border border-white/15 bg-white/5 px-4 py-4">
                        <p class="text-slate-400">Ambiente</p>
                        <p class="mt-1 font-medium text-white">Operação protegida</p>
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/5 px-4 py-4">
                        <p class="text-slate-400">Confiabilidade</p>
                        <p class="mt-1 font-medium text-white">Infraestrutura estável</p>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <div class="mx-auto w-full max-w-md rounded-3xl border border-white/15 bg-slate-900/80 p-6 shadow-2xl backdrop-blur-xl sm:p-8">
                    <div class="mb-7 text-center lg:text-left">
                        <h2 class="text-3xl font-semibold text-white">Acesso ao painel</h2>
                        <p class="mt-2 text-sm text-slate-400">Entre com suas credenciais para continuar.</p>
                    </div>

                    <x-auth-session-status class="mb-4 rounded-lg border border-emerald-400/30 bg-emerald-500/10 px-3 py-2 text-emerald-300" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-sm font-medium text-slate-200">E-mail</label>
                            <x-text-input id="email"
                                class="block w-full rounded-xl border border-slate-600 bg-slate-800/70 px-4 py-3 text-sm text-white placeholder-slate-400 focus:border-cyan-400 focus:ring-cyan-400"
                                type="email"
                                name="email"
                                :value="old('email')"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="seuemail@empresa.com" />
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-300" />
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Senha</label>
                            <x-text-input id="password"
                                class="block w-full rounded-xl border border-slate-600 bg-slate-800/70 px-4 py-3 text-sm text-white placeholder-slate-400 focus:border-cyan-400 focus:ring-cyan-400"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                placeholder="Digite sua senha" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-300" />
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label for="remember_me" class="inline-flex cursor-pointer items-center gap-2 text-sm text-slate-300">
                                <input id="remember_me"
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-slate-500 bg-slate-800 text-cyan-500 focus:ring-cyan-400"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <span>Lembrar-me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-cyan-300 transition hover:text-cyan-200">
                                    Esqueci minha senha
                                </a>
                            @endif
                        </div>

                        <button type="submit"
                            class="mt-2 inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-cyan-500 to-indigo-500 px-4 py-3 text-sm font-semibold text-white shadow-lg shadow-cyan-500/20 transition hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-900">
                            Entrar
                        </button>
                    </form>

                    <div class="mt-6 flex items-center justify-center gap-2 text-xs text-slate-400">
                        <svg class="h-4 w-4 text-emerald-300" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 1a4 4 0 00-4 4v2H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2V9a2 2 0 00-2-2h-1V5a4 4 0 00-4-4zM8 7V5a2 2 0 114 0v2H8z" clip-rule="evenodd" />
                        </svg>
                        <span>Ambiente seguro e monitorado</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-guest-layout>
