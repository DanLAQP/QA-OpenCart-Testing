import { Controller } from '../component.js';
import { loader } from '../index.js';

// Language
const language = loader.language('catalog/category');

export default class extends Controller {
    render() {
        let data = {};

        return loader.template('catalog/category', { ...data, ...language });
    }
}