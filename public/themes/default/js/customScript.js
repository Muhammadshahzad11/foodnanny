"use strict";

/* Setting Responsive Menu Collapsed js Start */
document.addEventListener('DOMContentLoaded', function () {
    let toggleValue = false;
    document.querySelectorAll('.settings-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            toggleValue = !toggleValue;
            const pixel = this.nextElementSibling.scrollHeight;
            if (toggleValue) {
                this.nextElementSibling.style.height = `${pixel}px`;
                const icon                           = this.querySelector('.rotating-icon');
                icon.classList.add('rotate-180');
            } else {
                this.nextElementSibling.style.height = `0px`;
                const icon                           = this.querySelector('.rotating-icon');
                icon.classList.remove('rotate-180');
            }
        });
    });
});
/* Setting Responsive Menu Collapsed js End */


/* Installer js Start */
const handleLinkForInstaller = (param) => {
    window.location.replace(param);
};

document.getElementById('installer-link')?.addEventListener('click', (e) => {
    document.getElementById('installer-modal')?.classList.add('active');
});

document.getElementById('installer-modal-close')?.addEventListener('click', (e) => {
    document.getElementById('installer-modal')?.classList.remove('active');
});
/* Installer js End */


/* Screen esc method start */
function handleMouseMove(event) {
    const mainElement   = document?.querySelector("main");
    const headerElement = document?.getElementById("backend-header");

    if (event.clientY <= 100) {
        mainElement.classList.add("pt-[70px]", "md:pt-16");
        headerElement.classList.remove("hidden");
    } else {
        mainElement.classList.remove("pt-[70px]", "md:pt-16");
        headerElement.classList.add("hidden");
    }
}

document.addEventListener('keydown', function (event) {
    if (event.key === "Escape" || event.keyCode === 27 || event.keyCode === 18 || event.keyCode === 91) {
        event.preventDefault();
        if (document.fullscreenElement) {
            document.exitFullscreen().catch(err => {
                console.error('Error exiting fullscreen:', err);
            });
        }
        handleExitFullscreen();
    }
});

function handleExitFullscreen() {
    const mainElement   = document?.querySelector("main");
    const headerElement = document?.getElementById("backend-header");

    if (mainElement) {
        mainElement.classList.add("pt-[70px]", "md:pt-16");
    }

    if (headerElement) {
        headerElement.classList.remove("hidden");
    }

    document.removeEventListener('mousemove', handleMouseMove);
}
/* Screen esc method end */
