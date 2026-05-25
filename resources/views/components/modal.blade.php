@props([
    'id',
    'title',
    'description' => null,
    'maxWidth' => 'max-w-md',
])

<div
    id="{{ $id }}"
    class="fixed inset-0 z-[1000] hidden items-center justify-center p-4 before:fixed before:inset-0 before:h-full before:w-full before:bg-[rgba(0,0,0,0.5)]"
>
    <div
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $id }}-title"
        tabindex="-1"
        class="relative max-h-[95vh] w-full {{ $maxWidth }} overflow-y-auto rounded-3xl border border-slate-100 bg-white p-5 shadow-xl outline-none md:p-6"
    >
        <button
            type="button"
            data-modal-close="{{ $id }}"
            aria-label="Close modal"
            class="absolute top-6 right-6 flex items-center rounded focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="size-3 cursor-pointer fill-slate-500 hover:fill-red-600" aria-hidden="true" viewBox="0 0 329.269 329">
                <path d="M194.8 164.77 323.013 36.555c8.343-8.34 8.343-21.825 0-30.164-8.34-8.34-21.825-8.34-30.164 0L164.633 134.605 36.422 6.391c-8.344-8.34-21.824-8.34-30.164 0-8.344 8.34-8.344 21.824 0 30.164l128.21 128.215L6.259 292.984c-8.344 8.34-8.344 21.825 0 30.164a21.27 21.27 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25l128.21-128.214 128.216 128.214a21.27 21.27 0 0 0 15.082 6.25c5.46 0 10.922-2.09 15.082-6.25 8.343-8.34 8.343-21.824 0-30.164zm0 0" />
            </svg>
        </button>

        <div class="mt-4 text-center">
            <h3 id="{{ $id }}-title" class="text-2xl font-semibold text-slate-900">{{ $title }}</h3>

            @if ($description)
                <p class="mt-3 text-sm leading-relaxed text-slate-600">
                    {{ $description }}
                </p>
            @endif
        </div>

        <div class="mt-6">
            {{ $slot }}
        </div>
    </div>
</div>
