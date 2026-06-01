<div>
    <label for="{{ $field }}" class="mb-2 block text-sm font-medium text-slate-700">
        {{ $label }}
    </label>

    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div id="{{ $toolbarId }}" class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-3">
            <button type="button" data-editor="h2" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M2.75 4a.75.75 0 0 1 .75.75v4.5h5v-4.5a.75.75 0 0 1 1.5 0v10.5a.75.75 0 0 1-1.5 0v-4.5h-5v4.5a.75.75 0 0 1-1.5 0V4.75A.75.75 0 0 1 2.75 4ZM15 9.5c-.729 0-1.445.051-2.146.15a.75.75 0 0 1-.208-1.486 16.887 16.887 0 0 1 3.824-.1c.855.074 1.512.78 1.527 1.637a17.476 17.476 0 0 1-.009.931 1.713 1.713 0 0 1-1.18 1.556l-2.453.818a1.25 1.25 0 0 0-.855 1.185v.309h3.75a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1-.75-.75v-1.059a2.75 2.75 0 0 1 1.88-2.608l2.454-.818c.102-.034.153-.117.155-.188a15.556 15.556 0 0 0 .009-.85.171.171 0 0 0-.158-.169A15.458 15.458 0 0 0 15 9.5Z" clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" data-editor="h3" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M2.75 4a.75.75 0 0 1 .75.75v4.5h5v-4.5a.75.75 0 0 1 1.5 0v10.5a.75.75 0 0 1-1.5 0v-4.5h-5v4.5a.75.75 0 0 1-1.5 0V4.75A.75.75 0 0 1 2.75 4ZM15 9.5c-.73 0-1.448.051-2.15.15a.75.75 0 1 1-.209-1.485 16.886 16.886 0 0 1 3.476-.128c.985.065 1.878.837 1.883 1.932V10a6.75 6.75 0 0 1-.301 2A6.75 6.75 0 0 1 18 14v.031c-.005 1.095-.898 1.867-1.883 1.932a17.018 17.018 0 0 1-3.467-.127.75.75 0 0 1 .209-1.485 15.377 15.377 0 0 0 3.16.115c.308-.02.48-.24.48-.441L16.5 14c0-.431-.052-.85-.15-1.25h-2.6a.75.75 0 0 1 0-1.5h2.6c.098-.4.15-.818.15-1.25v-.024c-.001-.201-.173-.422-.481-.443A15.485 15.485 0 0 0 15 9.5Z" clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" data-editor="bold" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M4 3a1 1 0 0 1 1-1h6a4.5 4.5 0 0 1 3.274 7.587A4.75 4.75 0 0 1 11.25 18H5a1 1 0 0 1-1-1V3Zm2.5 5.5v-4H11a2 2 0 1 1 0 4H6.5Zm0 2.5v4.5h4.75a2.25 2.25 0 0 0 0-4.5H6.5Z" clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" data-editor="italic" class="rounded-xl border border-slate-300 px-3 py-2 text-sm italic text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M8 2.75A.75.75 0 0 1 8.75 2h7.5a.75.75 0 0 1 0 1.5h-3.215l-4.483 13h2.698a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1 0-1.5h3.215l4.483-13H8.75A.75.75 0 0 1 8 2.75Z" clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" data-editor="bulletList" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M6 4.75A.75.75 0 0 1 6.75 4h10.5a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 4.75ZM6 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 10Zm0 5.25a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H6.75a.75.75 0 0 1-.75-.75ZM1.99 4.75a1 1 0 0 1 1-1H3a1 1 0 0 1 1 1v.01a1 1 0 0 1-1 1h-.01a1 1 0 0 1-1-1v-.01ZM1.99 15.25a1 1 0 0 1 1-1H3a1 1 0 0 1 1 1v.01a1 1 0 0 1-1 1h-.01a1 1 0 0 1-1-1v-.01ZM1.99 10a1 1 0 0 1 1-1H3a1 1 0 0 1 1 1v.01a1 1 0 0 1-1 1h-.01a1 1 0 0 1-1-1V10Z" clip-rule="evenodd" />
                </svg>
            </button>
            <button type="button" data-editor="orderedList" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path d="M3 1.25a.75.75 0 0 0 0 1.5h.25v2.5a.75.75 0 0 0 1.5 0V2A.75.75 0 0 0 4 1.25H3ZM2.97 8.654a3.5 3.5 0 0 1 1.524-.12.034.034 0 0 1-.012.012L2.415 9.579A.75.75 0 0 0 2 10.25v1c0 .414.336.75.75.75h2.5a.75.75 0 0 0 0-1.5H3.927l1.225-.613c.52-.26.848-.79.848-1.371 0-.647-.429-1.327-1.193-1.451a5.03 5.03 0 0 0-2.277.155.75.75 0 0 0 .44 1.434ZM7.75 3a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5ZM7.75 9.25a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5ZM7.75 15.5a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5ZM2.625 13.875a.75.75 0 0 0 0 1.5h1.5a.125.125 0 0 1 0 .25H3.5a.75.75 0 0 0 0 1.5h.625a.125.125 0 0 1 0 .25h-1.5a.75.75 0 0 0 0 1.5h1.5a1.625 1.625 0 0 0 1.37-2.5 1.625 1.625 0 0 0-1.37-2.5h-1.5Z" />
                </svg>
            </button>
            <button type="button" data-editor="quote" class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                    <path fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9.937 10.453l-.01.13c0 3.35-2.038 5.115-4.63 6.058m4.64-6.188a3.093 3.093 0 1 1-6.187.001a3.093 3.093 0 0 1 6.187-.001m10.313 0l-.01.13c0 3.35-2.038 5.115-4.63 6.058m4.64-6.188a3.093 3.093 0 1 1-6.187 0a3.093 3.093 0 0 1 6.187 0"/>
                </svg>
            </button>
        </div>

        <div
            id="{{ $editorId }}"
            class="min-h-80 px-5 py-4 text-base leading-8 text-slate-900 focus:outline-none [&_.ProseMirror]:min-h-80 [&_.ProseMirror]:outline-none [&_.ProseMirror_blockquote]:border-l-4 [&_.ProseMirror_blockquote]:border-blue-200 [&_.ProseMirror_blockquote]:pl-4 [&_.ProseMirror_blockquote]:italic [&_.ProseMirror_h2]:mb-3 [&_.ProseMirror_h2]:mt-6 [&_.ProseMirror_h2]:text-2xl [&_.ProseMirror_h2]:font-semibold [&_.ProseMirror_h3]:mb-3 [&_.ProseMirror_h3]:mt-5 [&_.ProseMirror_h3]:text-xl [&_.ProseMirror_h3]:font-semibold [&_.ProseMirror_ol]:my-4 [&_.ProseMirror_ol]:list-decimal [&_.ProseMirror_ol]:pl-6 [&_.ProseMirror_p]:mb-4 [&_.ProseMirror_ul]:my-4 [&_.ProseMirror_ul]:list-disc [&_.ProseMirror_ul]:pl-6"
        ></div>
    </div>

    <textarea id="{{ $field }}" name="{{ $field }}" class="hidden">{{ $value }}</textarea>

    @error($field)
        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>
