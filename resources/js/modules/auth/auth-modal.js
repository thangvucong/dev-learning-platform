document.addEventListener('DOMContentLoaded', function () {
    var modal = document.getElementById('auth-modal');

    if (!modal) {
        return;
    }

    var switchButtons = Array.prototype.slice.call(document.querySelectorAll('[data-auth-switch]'));
    var openButtons = Array.prototype.slice.call(document.querySelectorAll('[data-auth-open]'));
    var closeButtons = Array.prototype.slice.call(document.querySelectorAll('[data-auth-close]'));
    var initialTab = modal.dataset.authInitialTab || 'login';
    var shouldOpenOnLoad = modal.dataset.authOpenOnLoad === '1';
    var panels = {
        login: modal.querySelector('[data-auth-panel="login"]'),
        register: modal.querySelector('[data-auth-panel="register"]'),
    };

    function setTab(tab) {
        var activeTab = tab === 'register' ? 'register' : 'login';

        Object.keys(panels).forEach(function (key) {
            if (!panels[key]) {
                return;
            }

            panels[key].classList.toggle('hidden', key !== activeTab);
        });

        switchButtons.forEach(function (button) {
            var isActive = button.dataset.authSwitch === activeTab;
            button.classList.toggle('bg-white', isActive);
            button.classList.toggle('text-[#292929]', isActive);
            button.classList.toggle('shadow-sm', isActive);
            button.classList.toggle('text-[#808080]', !isActive);
        });
    }

    function openModal(tab) {
        setTab(tab || 'login');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    }

    openButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            openModal(button.dataset.authOpen);
        });
    });

    switchButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            setTab(button.dataset.authSwitch);
        });
    });

    closeButtons.forEach(function (button) {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', function (event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });

    setTab(initialTab);

    if (shouldOpenOnLoad) {
        openModal(initialTab);
    }
});
