import {h} from "vue";
import {useToast} from "vue-toastification";
import VueSimpleAlert from "vue3-simple-alert";
import router from "../router/index.js";
/*
 * Position
 * --------------
 * top-right
 * top-center
 * top-left
 * bottom-right
 * bottom-center
 * bottom-left
 * */
function navigateTo(url) {
    if (!url) return;
    router.push(url).catch(() => {
        window.location.href = url;
    });
}

function buildStatusContent(message, orderSerial, orderUrl) {
    const text = String(message || '');
    if (!orderUrl || !orderSerial) {
        return text;
    }

    const serial = String(orderSerial);
    const re = new RegExp(`(Order\\s*)?#?${serial.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')}`, 'i');
    const match = text.match(re) || text.match(/#?\d{6,}/);
    if (!match || match.index == null) {
        return h('span', {}, [
            text + ' ',
            h('a', {
                href: orderUrl,
                class: 'toast-order-link',
                style: {
                    color: 'inherit',
                    fontWeight: '800',
                    textDecoration: 'underline',
                    cursor: 'pointer',
                },
                onClick: (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    navigateTo(orderUrl);
                },
            }, `#${serial}`),
        ]);
    }

    const start = match.index;
    const end = start + match[0].length;
    const nodes = [];
    if (start > 0) nodes.push(text.slice(0, start));
    nodes.push(h('a', {
        href: orderUrl,
        class: 'toast-order-link',
        style: {
            color: 'inherit',
            fontWeight: '800',
            textDecoration: 'underline',
            cursor: 'pointer',
        },
        onClick: (e) => {
            e.preventDefault();
            e.stopPropagation();
            navigateTo(orderUrl);
        },
    }, match[0].startsWith('#') || /^order/i.test(match[0]) ? match[0] : `#${match[0]}`));
    if (end < text.length) nodes.push(text.slice(end));

    return h('span', {}, nodes);
}

export default {
    default: function (message = "Default", position = "top-right") {
        const toast = useToast();
        toast(message, {
            position: position,
        });
    },

    success: function (message = "Success", position = "top-right", timeout = null) {
        const toast = useToast();
        const options = {position: position};
        if (timeout != null) {
            options.timeout = timeout;
        }
        toast.success(message, options);
    },

    /**
     * Short-lived success toast for quick multi-step flows (e.g. restaurant signup).
     */
    successQuick: function (message = "Success", position = "top-right") {
        return this.success(message, position, 1400);
    },

    info: function (message = "Info", position = "top-right") {
        const toast = useToast();
        toast.info(message, {
            position: position,
        });
    },

    warning: function (message = "Warning", position = "top-right") {
        const toast = useToast();
        toast.warning(message, {
            position: position,
        });
    },

    error: function (message = "Error", position = "top-right") {
        const toast = useToast();
        // Errors must be readable: they outlive the quick success toasts and
        // hold while the pointer is over them.
        toast.error(message, {
            position: position,
            timeout: 5000,
            pauseOnHover: true,
            closeButton: "button",
        });
    },

    /**
     * Status / kitchen alert bar. Order numbers are clickable.
     */
    statusAlert: function (message = "Update", tone = "info", timeout = 2000, meta = {}) {
        const toast = useToast();
        const duration = Number(timeout) > 0 ? Number(timeout) : 2000;
        const content = buildStatusContent(message, meta.orderSerial, meta.orderUrl);
        const options = {
            position: "top-right",
            timeout: duration,
            closeOnClick: false,
            pauseOnHover: true,
            pauseOnFocusLoss: true,
            showCloseButtonOnHover: false,
            closeButton: "button",
            hideProgressBar: false,
        };

        if (tone === "error" || tone === "rose") {
            return toast.error(content, options);
        }
        if (tone === "success" || tone === "emerald") {
            return toast.success(content, options);
        }
        if (tone === "warning" || tone === "amber") {
            return toast.warning(content, options);
        }
        return toast.info(content, options);
    },

    /**
     * New order arrived — large, long-lived and clickable, unlike the routine
     * status toasts.
     */
    newOrderAlert: function (message = "New order", meta = {}) {
        const toast = useToast();
        const content = buildStatusContent(message, meta.orderSerial, meta.orderUrl);

        return toast.success(content, {
            position: "top-right",
            timeout: Number(meta.timeout) > 0 ? Number(meta.timeout) : 12000,
            toastClassName: "toast-new-order",
            closeOnClick: false,
            closeButton: "button",
            pauseOnHover: true,
            pauseOnFocusLoss: true,
            hideProgressBar: false,
        });
    },

    /**
     * Dummy OTP popup is disabled. Live OTP is sent by 2Factor SMS.
     */
    showOtp: function (otp) {
        return Promise.resolve(false);
    },

    successFlip: function (status = null, message = "", position = "top-right") {
        const toast = useToast();
        if (status != null) {
            if (status) {
                message = message + " Updated Successfully.";
            } else {
                message = message + " Created Successfully.";
            }
        } else {
            message = message + " Deleted Successfully.";
        }

        toast.success(message, {
            position: position,
        });
    },

    successInfo: function (status = null, message = "", position = "top-right") {
        const toast = useToast();
        toast.success(message, {
            position: position,
        });
    },
};
