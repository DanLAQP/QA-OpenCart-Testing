import { Controller } from '../component.js';
import { loader } from '../index.js';

// Language
const language = await loader.language('catalog/special');

export default class extends Controller {
    async connected() {

    }

    render() {
        return loader.template('catalog/special', { ...language });
    }
}