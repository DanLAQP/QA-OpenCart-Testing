import { Controller } from '../component.js';
import { loader } from '../index.js';

export default class extends Controller {
    async render() {
        let data = {};



        return this.render('catalog/product_info', { ...data, ...language, ...config });
    }
}