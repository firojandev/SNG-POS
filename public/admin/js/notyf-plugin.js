import { Notyf } from 'notyf';
import 'notyf/notyf.min.css';

export default {
    install(app) {
        const notyf = new Notyf();

        function showNotification(type, message) {
            if (type === 'success') {
                notyf.success(message);
            } else if (type === 'error') {
                notyf.error(message);
            } else {
                notyf.open({ type: type, message: message });
            }
        }

        // Make available as this.$showNotification
        app.config.globalProperties.$showNotification = showNotification;

        // Optional: If you want this.NotyfMessage.showNotification()
        app.config.globalProperties.$NotyfMessage = {
            showNotification
        };
    }
};
