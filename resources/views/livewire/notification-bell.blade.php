<div class="relative" x-data @click.outside="$wire.open = false">
    <button type="button" wire:click="toggle" class="relative rounded-lg p-1.5 text-neutral-500 hover:bg-neutral-100 hover:text-brand-600">
        <x-heroicon-o-bell class="h-5 w-5" />
        @if ($unreadCount > 0)
            <span class="absolute -right-0.5 -top-0.5 flex h-4 min-w-4 items-center justify-center rounded-full bg-gold-500 px-1 text-[10px] font-semibold text-white">
                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
            </span>
        @endif
    </button>

    @if ($open)
        <div class="absolute right-0 z-20 mt-2 w-80 rounded-xl border border-neutral-200 bg-white shadow-lg">
            <div class="flex items-center justify-between border-b border-neutral-100 px-4 py-2.5">
                <span class="text-sm font-semibold text-neutral-800">Notifikasi</span>
                @if ($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-xs font-medium text-brand-600 hover:text-brand-700">Tandai semua dibaca</button>
                @endif
            </div>

            <ul class="max-h-80 divide-y divide-neutral-100 overflow-y-auto">
                @forelse ($notifications as $notification)
                    <li wire:click="markAsRead('{{ $notification->id }}')"
                        class="cursor-pointer px-4 py-3 text-sm hover:bg-neutral-50 {{ $notification->read_at ? '' : 'bg-brand-50/40' }}">
                        <p class="font-medium text-neutral-800">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                        <p class="mt-0.5 text-xs text-neutral-500">{{ $notification->data['body'] ?? '' }}</p>
                        <p class="mt-1 text-[11px] text-neutral-400">{{ $notification->created_at->diffForHumans() }}</p>
                    </li>
                @empty
                    <li class="px-4 py-6 text-center text-sm text-neutral-400">Belum ada notifikasi.</li>
                @endforelse
            </ul>
        </div>
    @endif
</div>
