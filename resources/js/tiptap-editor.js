import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'

function getEmptyDocument() {
    return {
        type: 'doc',
        content: [
            {
                type: 'paragraph',
            },
        ],
    }
}

function parseEditorContent(value) {
    if (!value) {
        return getEmptyDocument()
    }

    try {
        return JSON.parse(value)
    } catch {
        return getEmptyDocument()
    }
}

function getNormalizedEditorValue(editor) {
    if (editor.isEmpty) {
        return ''
    }

    return JSON.stringify(editor.getJSON())
}

function normalizeUrl(value) {
    const url = value.trim()

    if (!url) {
        return ''
    }

    if (
        url.startsWith('http://') ||
        url.startsWith('https://') ||
        url.startsWith('mailto:') ||
        url.startsWith('/')
    ) {
        return url
    }

    return `https://${url}`
}

function getSelectedText(editor) {
    const { from, to } = editor.state.selection

    if (from === to) {
        return ''
    }

    return editor.state.doc.textBetween(from, to, ' ')
}


function createEditor({ element, hiddenField, toolbar }) {
    if (!element || !hiddenField) {
        return null
    }

    let updateToolbarState = () => { }

    const editor = new Editor({
        element,
        extensions: [
            StarterKit.configure({
                heading: {
                    levels: [2, 3],
                },
                link: false,
            }),
            Link.configure({
                openOnClick: false,
                autolink: true,
                linkOnPaste: true,
                HTMLAttributes: {
                    class: 'text-blue-700 underline underline-offset-2',
                    rel: 'noopener noreferrer',
                    target: '_blank',
                },
            }),
        ],
        content: parseEditorContent(hiddenField.value),
        onUpdate: ({ editor: currentEditor }) => {
            hiddenField.value = getNormalizedEditorValue(currentEditor)
            updateToolbarState()
        },
        onSelectionUpdate: () => {
            updateToolbarState()
        },
        onTransaction: () => {
            updateToolbarState()
        },
    })

    const setButtonActive = (name, isActive) => {
        const button = toolbar?.querySelector(`[data-editor="${name}"]`)

        if (!button) {
            return
        }

        button.classList.toggle('border-blue-500', isActive)
        button.classList.toggle('bg-blue-50', isActive)
        button.classList.toggle('text-blue-700', isActive)

        button.classList.toggle('border-slate-300', !isActive)
        button.classList.toggle('text-slate-700', !isActive)
    }

    updateToolbarState = () => {
        setButtonActive('h2', editor.isActive('heading', { level: 2 }))
        setButtonActive('h3', editor.isActive('heading', { level: 3 }))
        setButtonActive('bold', editor.isActive('bold'))
        setButtonActive('italic', editor.isActive('italic'))
        setButtonActive('quote', editor.isActive('blockquote'))
        setButtonActive('bulletList', editor.isActive('bulletList'))
        setButtonActive('orderedList', editor.isActive('orderedList'))
        setButtonActive('link', editor.isActive('link'))
    }

    const bindCommand = (selector, callback) => {
        toolbar?.querySelector(selector)?.addEventListener('click', () => {
            callback()
            updateToolbarState()
        })
    }

    const linkPopover = element.closest('[data-tiptap-editor-root]')?.querySelector('[data-link-popover]')
    const linkLabelWrap = linkPopover?.querySelector('[data-link-label-wrap]')
    const linkLabelInput = linkPopover?.querySelector('[data-link-label-input]')
    const linkUrlInput = linkPopover?.querySelector('[data-link-url-input]')
    const linkApplyButton = linkPopover?.querySelector('[data-link-apply]')
    const linkCancelButton = linkPopover?.querySelector('[data-link-cancel]')

    let savedSelection = null
    let linkMode = 'selected'


    bindCommand('[data-editor="h2"]', () => {
        editor.chain().focus().toggleHeading({ level: 2 }).run()
    })

    bindCommand('[data-editor="h3"]', () => {
        editor.chain().focus().toggleHeading({ level: 3 }).run()
    })

    bindCommand('[data-editor="bold"]', () => {
        editor.chain().focus().toggleBold().run()
    })

    bindCommand('[data-editor="italic"]', () => {
        editor.chain().focus().toggleItalic().run()
    })

    bindCommand('[data-editor="quote"]', () => {
        editor.chain().focus().toggleBlockquote().run()
    })

    bindCommand('[data-editor="bulletList"]', () => {
        editor.chain().focus().toggleBulletList().run()
    })

    bindCommand('[data-editor="orderedList"]', () => {
        editor.chain().focus().toggleOrderedList().run()
    })

    bindCommand('[data-editor="link"]', () => {
        const selectedText = getSelectedText(editor)
        const previousUrl = editor.getAttributes('link').href ?? ''

        savedSelection = {
            from: editor.state.selection.from,
            to: editor.state.selection.to,
        }
        linkMode = selectedText ? 'selected' : 'insert'

        if (linkLabelWrap) {
            linkLabelWrap.classList.toggle('hidden', linkMode === 'selected')
        }

        if (linkLabelInput) {
            linkLabelInput.value = selectedText
        }

        if (linkUrlInput) {
            linkUrlInput.value = previousUrl
        }

        linkPopover?.classList.remove('hidden')
        linkPopover?.classList.add('flex')

        setTimeout(() => {
            if (linkMode === 'insert') {
                linkLabelInput?.focus()
                return
            }

            linkUrlInput?.focus()
        }, 50)
    })

    linkCancelButton?.addEventListener('click', () => {
        linkPopover?.classList.add('hidden')
        linkPopover?.classList.remove('flex')
    })

    linkPopover?.addEventListener('click', (event) => {
        if (event.target === linkPopover) {
            linkPopover.classList.add('hidden')
            linkPopover.classList.remove('flex')
        }
    })

    linkApplyButton?.addEventListener('click', () => {
        const url = normalizeUrl(linkUrlInput?.value ?? '')
        const label = linkLabelInput?.value.trim() ?? ''

        if (!url) {
            return
        }

        if (savedSelection) {
            editor.commands.setTextSelection(savedSelection)
        }

        if (linkMode === 'selected') {
            editor
                .chain()
                .focus()
                .extendMarkRange('link')
                .setLink({ href: url })
                .run()
        } else {
            if (!label) {
                return
            }

            editor
                .chain()
                .focus()
                .insertContent({
                    type: 'text',
                    text: label,
                    marks: [
                        {
                            type: 'link',
                            attrs: {
                                href: url,
                            },
                        },
                    ],
                })
                .run()
        }

        hiddenField.value = getNormalizedEditorValue(editor)

        linkPopover?.classList.add('hidden')
        linkPopover?.classList.remove('flex')
    })

    hiddenField.value = getNormalizedEditorValue(editor)
    updateToolbarState()

    return editor
}

export function mountTiptapEditors() {
    const roots = document.querySelectorAll('[data-tiptap-editor-root]')

    roots.forEach((root) => {
        const editorElement = root.querySelector('[data-editor-surface]')
        const toolbar = root.querySelector('[data-editor-toolbar]')
        const hiddenField = root.querySelector('[data-editor-input]')

        createEditor({
            element: editorElement,
            hiddenField,
            toolbar,
        })
    })
}