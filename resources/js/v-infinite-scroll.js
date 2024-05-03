export default {
    mounted(el, binding) {
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1,
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    binding.value();
                }
            });
        }, options);

        observer.observe(el);

        el._intersectionObserver = observer;
    },
    beforeUnmount(el) {
        el._intersectionObserver.disconnect();
    },
};
