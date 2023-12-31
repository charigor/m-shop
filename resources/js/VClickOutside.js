export default {

        mounted (el, {value})  {
            el.clickOutsideEvent = function (event) {
                if(!(el == event.target || el.contains(event.target))){
                    value(event)
                }
            }
// register click and touch events
            if (typeof document !== 'undefined') {
                document.body.addEventListener('click', el.clickOutsideEvent)
                document.body.addEventListener('touchstart', el.clickOutsideEvent)
            }
        },
        unmounted (el,{value}) {
// unregister click and touch events before the element is unmounted
            if (typeof document !== 'undefined') {
                document.body.removeEventListener('click', el.clickOutsideEvent)
                document.body.removeEventListener('touchstart', el.clickOutsideEvent)
            }
        }

    }

