<div class="flex min-h-screen items-center justify-center px-4">
    <div class="w-full max-w-sm rounded-2xl border border-brand-100 bg-white p-8 shadow-sm">
        <div class="mb-6 text-center">
            <h1 class="text-lg font-semibold text-brand-700">Az-Zahra Digital Campus</h1>
            <p class="mt-1 text-sm text-neutral-500">Masuk ke akun Anda</p>
        </div>

        <form wire:submit="authenticate" class="space-y-4">
            <div>
                <label for="login" class="block text-sm font-medium text-neutral-700">Email / Username</label>
                <input wire:model="login" id="login" type="text" autofocus
                    class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('login') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-neutral-700">Password</label>
                <input wire:model="password" id="password" type="password"
                    class="mt-1 block w-full rounded-lg border border-neutral-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-brand-500">
                @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
            </div>

            <label class="flex items-center gap-2 text-sm text-neutral-600">
                <input wire:model="remember" type="checkbox" class="rounded border-neutral-300 text-brand-600">
                Ingat saya
            </label>

            <button type="submit"
                class="w-full rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-brand-700"
                wire:loading.attr="disabled" wire:target="authenticate">
                <span wire:loading.remove wire:target="authenticate">Masuk</span>
                <span wire:loading wire:target="authenticate">Memproses…</span>
            </button>
        </form>
    </div>
</div>
