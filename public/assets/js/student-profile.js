/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!*****************************************!*\
  !*** ./resources/js/student-profile.js ***!
  \*****************************************/
document.addEventListener('DOMContentLoaded', function () {
  var root = document.getElementById('student-profile-root');
  if (!root) return;
  var buttons = Array.prototype.slice.call(document.querySelectorAll('.profile-tab-btn'));
  var panels = Array.prototype.slice.call(document.querySelectorAll('.profile-tab-panel'));
  var modals = Array.prototype.slice.call(document.querySelectorAll('[data-modal]'));
  function activateTab(target) {
    buttons.forEach(function (btn) {
      var isActive = btn.getAttribute('data-tab-target') === target;
      btn.classList.toggle('bg-emerald-500', isActive);
      btn.classList.toggle('text-white', isActive);
      btn.classList.toggle('text-slate-300', !isActive);
      btn.classList.toggle('hover:bg-slate-700', !isActive);
    });
    panels.forEach(function (panel) {
      var isActive = panel.getAttribute('data-tab-panel') === target;
      panel.classList.toggle('hidden', !isActive);
    });
  }
  function closeModal(modal) {
    if (!modal) return;
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }
  function openModalById(modalId) {
    var modal = document.getElementById(modalId);
    if (!modal) return;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }
  buttons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      activateTab(btn.getAttribute('data-tab-target'));
    });
  });
  document.querySelectorAll('[data-modal-open]').forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var target = trigger.getAttribute('data-modal-open');
      if (target) {
        openModalById(target);
      }
    });
  });
  modals.forEach(function (modal) {
    modal.querySelectorAll('[data-modal-close]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        closeModal(modal);
      });
    });
  });
  document.addEventListener('keydown', function (event) {
    if (event.key !== 'Escape') return;
    modals.forEach(function (modal) {
      if (!modal.classList.contains('hidden')) {
        closeModal(modal);
      }
    });
  });
  document.querySelectorAll('[data-password-toggle]').forEach(function (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      var wrapper = toggleBtn.closest('.relative');
      var input = wrapper ? wrapper.querySelector('[data-password-input]') : null;
      if (!input) return;
      var isPassword = input.getAttribute('type') === 'password';
      input.setAttribute('type', isPassword ? 'text' : 'password');
      var icon = toggleBtn.querySelector('i');
      if (icon) {
        icon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
      }
    });
  });
  var avatarInput = document.getElementById('avatar-upload-input');
  var avatarPreview = document.getElementById('avatar-preview-image');
  if (avatarInput && avatarPreview) {
    avatarInput.addEventListener('change', function () {
      if (!avatarInput.files || !avatarInput.files.length) return;
      var file = avatarInput.files[0];
      var reader = new FileReader();
      reader.onload = function (event) {
        if (event.target && typeof event.target.result === 'string') {
          avatarPreview.setAttribute('src', event.target.result);
        }
      };
      reader.readAsDataURL(file);
    });
  }
  document.querySelectorAll('[data-form-loading]').forEach(function (form) {
    form.addEventListener('submit', function () {
      var submitButton = form.querySelector('button[type="submit"]');
      if (!submitButton) return;
      submitButton.setAttribute('disabled', 'disabled');
      submitButton.classList.add('opacity-70', 'cursor-not-allowed');
      var loadingText = submitButton.getAttribute('data-loading-text');
      if (loadingText) {
        submitButton.setAttribute('data-default-text', submitButton.textContent || '');
        submitButton.textContent = loadingText;
      }
    });
  });
  var initialModal = root.getAttribute('data-initial-modal') || '';
  if (initialModal) {
    openModalById(initialModal);
  }
});
/******/ })()
;