import { Controller } from '../component.js';
import { loader } from '../index.js';

// Language
const language = await loader.language('information/sitemap');

export default class extends Controller {
    render() {
        let data = {};

        data.categories = {};

        return loader.template('information/sitemap', { ...data, ...language });
    }
}