<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipper Registration - CargoTrace</title>
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
        <div class="text-center mb-6">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-600 to-sky-400 mx-auto flex items-center justify-center text-white shadow-xl shadow-cyan-500/25 mb-3">
                <i class="fa-solid fa-ship text-xl"></i>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">CargoTrace</h1>
            <p class="text-xs text-slate-400 mt-1">Register Customer / Shipper Account</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.post') }}" method="POST" class="space-y-3.5">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Full Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Elena Rostova"
                    class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Company / Organization</label>
                <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="e.g. Apex Global Logistics"
                    class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Email Address *</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="shipper@company.com"
                    class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Password *</label>
                    <input type="password" name="password" required placeholder="Min 8 chars"
                        class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Confirm Password *</label>
                    <input type="password" name="password_confirmation" required placeholder="Repeat password"
                        class="w-full py-2 px-3 text-xs bg-slate-50 border border-slate-200 rounded-xl outline-none focus:ring-2 focus:ring-cyan-500">
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-md mt-2">
                Complete Registration
            </button>
        </form>

        <p class="text-center text-xs text-slate-400 mt-6">
            Already registered? <a href="{{ route('login') }}" class="text-cyan-600 font-bold hover:underline">Sign In</a>
        </p>
    </div>

</body>
</html>
