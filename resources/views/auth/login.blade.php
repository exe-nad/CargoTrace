<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CargoTrace Visibility Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-3xl p-8 sm:p-10 shadow-2xl border border-slate-100">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-cyan-600 to-sky-400 mx-auto flex items-center justify-center text-white shadow-xl shadow-cyan-500/25 mb-4">
                <i class="fa-solid fa-ship text-2xl"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">CargoTrace</h1>
            <p class="text-xs text-slate-400 mt-1">Global Shipment Visibility & Tracking Platform</p>
        </div>

        @if($errors->any())
            <div class="mb-5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Email Address</label>
                <div class="relative">
                    <input type="email" id="emailInput" name="email" value="{{ old('email', 'admin@cargotrace.com') }}" required
                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition">
                    <i class="fa-regular fa-envelope absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" id="passwordInput" name="password" value="password123" required
                        class="w-full pl-10 pr-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500 focus:bg-white transition">
                    <i class="fa-solid fa-lock absolute left-3.5 top-3 text-slate-400 text-sm"></i>
                </div>
            </div>

            <div class="flex items-center justify-between text-xs pt-1">
                <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                    <input type="checkbox" name="remember" class="rounded text-cyan-600">
                    <span>Remember this device</span>
                </label>
            </div>

            <button type="submit" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm rounded-xl transition shadow-lg mt-2">
                Sign In to Platform &rarr;
            </button>
        </form>

        <!-- Quick Demo Accounts (UAS Evaluation Helper - Slide 18 & 22) -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <span class="text-[11px] uppercase font-bold text-slate-400 block mb-2 text-center">Quick Demo Evaluator Switcher</span>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" onclick="setDemo('admin@cargotrace.com', 'password123')"
                    class="p-2 text-center rounded-xl bg-slate-50 hover:bg-cyan-50 hover:border-cyan-200 border border-slate-200 text-xs transition">
                    <span class="font-bold text-slate-800 block text-[11px]">Admin</span>
                    <span class="text-[9px] text-slate-400">Sarah J.</span>
                </button>
                <button type="button" onclick="setDemo('operator@cargotrace.com', 'password123')"
                    class="p-2 text-center rounded-xl bg-slate-50 hover:bg-cyan-50 hover:border-cyan-200 border border-slate-200 text-xs transition">
                    <span class="font-bold text-slate-800 block text-[11px]">Operator</span>
                    <span class="text-[9px] text-slate-400">Alex R.</span>
                </button>
                <button type="button" onclick="setDemo('customer@globalindustries.com', 'password123')"
                    class="p-2 text-center rounded-xl bg-slate-50 hover:bg-cyan-50 hover:border-cyan-200 border border-slate-200 text-xs transition">
                    <span class="font-bold text-slate-800 block text-[11px]">Customer</span>
                    <span class="text-[9px] text-slate-400">David M.</span>
                </button>
            </div>
        </div>

        <p class="text-center text-xs text-slate-400 mt-6">
            New consignee shipper? <a href="{{ route('register') }}" class="text-cyan-600 font-bold hover:underline">Create account</a>
        </p>
    </div>

    <script>
        function setDemo(email, pass) {
            document.getElementById('emailInput').value = email;
            document.getElementById('passwordInput').value = pass;
        }
    </script>
</body>
</html>
