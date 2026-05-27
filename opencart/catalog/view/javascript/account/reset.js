import { Controller } from '../component.js';
import { loader } from '../index.js';

const language = await loader.language('account/reset');

export default class extends Controller {
    render() {


        return loader.template('account/reset', { ...language });
    }
}