import { mountArticleEditor, mountCareerEditors } from './article-editor'
import ApexCharts from 'apexcharts';

window.ApexCharts = ApexCharts;

document.addEventListener('DOMContentLoaded', () => {
    mountArticleEditor()
    mountCareerEditors()
})
