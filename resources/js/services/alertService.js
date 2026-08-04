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

    success: function (message = "Success", position = "top-right") {
        const toast = useToast();
        toast.success(message, {
            position: position,
        });
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
        toast.error(message, {
            position: position,
        });
    },

    /**
     * Sticky status / kitchen alert bar (30s). Order numbers are clickable.
     */
    statusAlert: function (message = "Update", tone = "info", timeout = 30000, meta = {}) {
        const toast = useToast();
        const duration = Number(timeout) > 0 ? Number(timeout) : 30000;
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
     * Temporary OTP popup until SMS credentials are configured.
     * Remove or gate this when real SMS delivery is enabled.
     */
    showOtp: function (otp) {
        if (!otp) {
            return Promise.resolve(false);
        }
        return VueSimpleAlert.alert(
            `Your OTP is: ${otp}\n\nTemporary popup — SMS gateway credentials are not configured yet.`,
            'OTP Code',
            'info'
        );
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
