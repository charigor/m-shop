
import Cleave from 'cleave.js';


export default {
    created: (el, binding) => {
        el.cleave = new Cleave(el, binding.value || {})
        console.log(el.cleave);
    },
    updated: (el) => {
        const event = new Event('input', {bubbles: true});
        setTimeout(function () {
            el.value = el.cleave.properties.result
            el.dispatchEvent(event)
        }, 100);
    }

}
