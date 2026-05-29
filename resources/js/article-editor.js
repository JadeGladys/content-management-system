import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'

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

function createEditor({ element, hiddenField, toolbar }) {
    if (!element || !hiddenField) {
        return null
    }

    const editor = new Editor({
        element,
        extensions: [
            StarterKit.configure({
                heading: {
                    levels: [2, 3],
                },
            }),
        ],
        content: parseEditorContent(hiddenField.value),
        onUpdate: ({ editor: currentEditor }) => {
            hiddenField.value = getNormalizedEditorValue(currentEditor)
        },
    })

    const bindCommand = (selector, callback) => {
        toolbar?.querySelector(selector)?.addEventListener('click', callback)
    }

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

    hiddenField.value = getNormalizedEditorValue(editor)

    return editor
}

export function mountArticleEditor() {
    const editorElement = document.getElementById('article-content-editor')
    const hiddenField = document.getElementById('content')
    const toolbar = document.getElementById('article-content-toolbar')

    return createEditor({
        element: editorElement,
        hiddenField,
        toolbar,
    })
}

export function mountCareerEditors() {
    const roots = document.querySelectorAll('[data-career-editor-root]')

    roots.forEach((root) => {
        const fieldName = root.dataset.careerEditorRoot
        const editorElement = root.querySelector('[data-editor-surface]')
        const toolbar = root.querySelector('[data-editor-toolbar]')
        const hiddenField = document.getElementById(fieldName)

        createEditor({
            element: editorElement,
            hiddenField,
            toolbar,
        })
    })
}
