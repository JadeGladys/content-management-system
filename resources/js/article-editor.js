import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'

export function mountArticleEditor() {
    const editorElement = document.getElementById('article-content-editor')
    const hiddenField = document.getElementById('content')

    if (!editorElement || !hiddenField) {
        return
    }

    const editor = new Editor({
        element: editorElement,
        extensions: [
            StarterKit.configure({
                heading: {
                    levels: [2, 3],
                },
            }),
        ],
        content: hiddenField.value ? JSON.parse(hiddenField.value) : {
            type: 'doc',
            content: [
                {
                    type: 'paragraph',
                },
            ],
        },
        onUpdate: ({ editor }) => {
            hiddenField.value = JSON.stringify(editor.getJSON())
        },
    })

    const bindCommand = (selector, callback) => {
        document.querySelector(selector)?.addEventListener('click', callback)
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

    hiddenField.value = JSON.stringify(editor.getJSON())

    return editor
}