import { mountTiptapEditors } from './tiptap-editor'
import ApexCharts from 'apexcharts';

window.ApexCharts = ApexCharts;

document.addEventListener('DOMContentLoaded', () => {
    mountTiptapEditors()
})
