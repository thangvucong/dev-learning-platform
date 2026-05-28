/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
/*!******************************************!*\
  !*** ./resources/js/student-schedule.js ***!
  \******************************************/
__webpack_require__.r(__webpack_exports__);
Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/core'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/daygrid'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/timegrid'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/list'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/interaction'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/daygrid/main.css'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/timegrid/main.css'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/list/main.css'"); e.code = 'MODULE_NOT_FOUND'; throw e; }());
function _regenerator() { /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/babel/babel/blob/main/packages/babel-helpers/LICENSE */ var e, t, r = "function" == typeof Symbol ? Symbol : {}, n = r.iterator || "@@iterator", o = r.toStringTag || "@@toStringTag"; function i(r, n, o, i) { var c = n && n.prototype instanceof Generator ? n : Generator, u = Object.create(c.prototype); return _regeneratorDefine2(u, "_invoke", function (r, n, o) { var i, c, u, f = 0, p = o || [], y = !1, G = { p: 0, n: 0, v: e, a: d, f: d.bind(e, 4), d: function d(t, r) { return i = t, c = 0, u = e, G.n = r, a; } }; function d(r, n) { for (c = r, u = n, t = 0; !y && f && !o && t < p.length; t++) { var o, i = p[t], d = G.p, l = i[2]; r > 3 ? (o = l === n) && (u = i[(c = i[4]) ? 5 : (c = 3, 3)], i[4] = i[5] = e) : i[0] <= d && ((o = r < 2 && d < i[1]) ? (c = 0, G.v = n, G.n = i[1]) : d < l && (o = r < 3 || i[0] > n || n > l) && (i[4] = r, i[5] = n, G.n = l, c = 0)); } if (o || r > 1) return a; throw y = !0, n; } return function (o, p, l) { if (f > 1) throw TypeError("Generator is already running"); for (y && 1 === p && d(p, l), c = p, u = l; (t = c < 2 ? e : u) || !y;) { i || (c ? c < 3 ? (c > 1 && (G.n = -1), d(c, u)) : G.n = u : G.v = u); try { if (f = 2, i) { if (c || (o = "next"), t = i[o]) { if (!(t = t.call(i, u))) throw TypeError("iterator result is not an object"); if (!t.done) return t; u = t.value, c < 2 && (c = 0); } else 1 === c && (t = i["return"]) && t.call(i), c < 2 && (u = TypeError("The iterator does not provide a '" + o + "' method"), c = 1); i = e; } else if ((t = (y = G.n < 0) ? u : r.call(n, G)) !== a) break; } catch (t) { i = e, c = 1, u = t; } finally { f = 1; } } return { value: t, done: y }; }; }(r, o, i), !0), u; } var a = {}; function Generator() {} function GeneratorFunction() {} function GeneratorFunctionPrototype() {} t = Object.getPrototypeOf; var c = [][n] ? t(t([][n]())) : (_regeneratorDefine2(t = {}, n, function () { return this; }), t), u = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(c); function f(e) { return Object.setPrototypeOf ? Object.setPrototypeOf(e, GeneratorFunctionPrototype) : (e.__proto__ = GeneratorFunctionPrototype, _regeneratorDefine2(e, o, "GeneratorFunction")), e.prototype = Object.create(u), e; } return GeneratorFunction.prototype = GeneratorFunctionPrototype, _regeneratorDefine2(u, "constructor", GeneratorFunctionPrototype), _regeneratorDefine2(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = "GeneratorFunction", _regeneratorDefine2(GeneratorFunctionPrototype, o, "GeneratorFunction"), _regeneratorDefine2(u), _regeneratorDefine2(u, o, "Generator"), _regeneratorDefine2(u, n, function () { return this; }), _regeneratorDefine2(u, "toString", function () { return "[object Generator]"; }), (_regenerator = function _regenerator() { return { w: i, m: f }; })(); }
function _regeneratorDefine2(e, r, n, t) { var i = Object.defineProperty; try { i({}, "", {}); } catch (e) { i = 0; } _regeneratorDefine2 = function _regeneratorDefine(e, r, n, t) { function o(r, n) { _regeneratorDefine2(e, r, function (e) { return this._invoke(r, n, e); }); } r ? i ? i(e, r, { value: n, enumerable: !t, configurable: !t, writable: !t }) : e[r] = n : (o("next", 0), o("throw", 1), o("return", 2)); }, _regeneratorDefine2(e, r, n, t); }
function asyncGeneratorStep(n, t, e, r, o, a, c) { try { var i = n[a](c), u = i.value; } catch (n) { return void e(n); } i.done ? t(u) : Promise.resolve(u).then(r, o); }
function _asyncToGenerator(n) { return function () { var t = this, e = arguments; return new Promise(function (r, o) { var a = n.apply(t, e); function _next(n) { asyncGeneratorStep(a, r, o, _next, _throw, "next", n); } function _throw(n) { asyncGeneratorStep(a, r, o, _next, _throw, "throw", n); } _next(void 0); }); }; }








document.addEventListener('DOMContentLoaded', function () {
  var config = window.StudentScheduleConfig || {};
  var dataUrl = config.dataUrl || '';
  var root = document.getElementById('student-calendar-root');
  var calendarEl = document.getElementById('student-calendar');
  var filterForm = document.getElementById('schedule-filter-form');
  if (!root || !calendarEl || !dataUrl) return;
  var initialEvents = JSON.parse(root.getAttribute('data-events') || '[]');
  var selectedSessionId = root.getAttribute('data-selected-session-id') || '';
  var initialView = root.getAttribute('data-initial-view') || 'timeGridWeek';
  var weekStart = root.getAttribute('data-week-start') || null;
  var loadingEl = document.getElementById('schedule-calendar-loading');
  var errorEl = document.getElementById('schedule-calendar-error');
  var dateRangeEl = document.getElementById('schedule-date-range');
  var detailRoot = document.getElementById('schedule-session-detail');
  var detailContentEl = document.getElementById('schedule-detail-content');
  var detailEmptyEl = document.getElementById('schedule-detail-empty');
  var statusEl = document.getElementById('schedule-detail-status');
  var joinBtn = document.getElementById('schedule-detail-join-btn');
  var statTotalEl = document.getElementById('schedule-stat-total');
  var statUpcomingEl = document.getElementById('schedule-stat-upcoming');
  var statLiveEl = document.getElementById('schedule-stat-live');
  var statMissedEl = document.getElementById('schedule-stat-missed');
  var statusClassMap = {
    upcoming: 'bg-blue-500/10 text-blue-300 border-blue-500/30',
    live: 'bg-emerald-500/20 text-emerald-200 border-emerald-400/40',
    completed: 'bg-slate-500/10 text-slate-300 border-slate-500/30',
    missed: 'bg-red-500/10 text-red-300 border-red-500/30'
  };
  var statusLabelMap = {
    upcoming: 'Sắp diễn ra',
    live: 'Đang diễn ra',
    completed: 'Đã kết thúc',
    missed: 'Đã lỡ buổi'
  };
  function formatDateRange(start, end) {
    var format = function format(d) {
      var dd = String(d.getDate()).padStart(2, '0');
      var mm = String(d.getMonth() + 1).padStart(2, '0');
      var yyyy = d.getFullYear();
      return "".concat(dd, "/").concat(mm, "/").concat(yyyy);
    };
    if (dateRangeEl) {
      dateRangeEl.textContent = "".concat(format(start), " - ").concat(format(end));
    }
  }
  function setActiveViewButton(viewType) {
    document.querySelectorAll('.schedule-view-btn').forEach(function (btn) {
      var isActive = btn.getAttribute('data-cal-view') === viewType;
      btn.classList.toggle('bg-emerald-500', isActive);
      btn.classList.toggle('text-white', isActive);
      btn.classList.toggle('text-slate-300', !isActive);
      btn.classList.toggle('hover:bg-slate-700', !isActive);
    });
  }
  function updateDetail(session) {
    if (!detailRoot) return;
    if (!session) {
      if (detailContentEl) detailContentEl.classList.add('hidden');
      if (detailEmptyEl) detailEmptyEl.classList.remove('hidden');
      if (joinBtn) {
        joinBtn.classList.add('hidden');
        joinBtn.removeAttribute('href');
      }
      return;
    }
    if (detailContentEl) detailContentEl.classList.remove('hidden');
    if (detailEmptyEl) detailEmptyEl.classList.add('hidden');
    ['class_name', 'course', 'teacher', 'start_at', 'meeting_info', 'description'].forEach(function (key) {
      var el = detailRoot.querySelector("[data-detail=\"".concat(key, "\"]"));
      if (el) el.textContent = session[key] || '';
    });
    if (statusEl) {
      var status = (session.status || 'upcoming').toLowerCase();
      statusEl.className = "inline-flex items-center px-3 py-1 rounded-full border text-xs uppercase font-semibold ".concat(statusClassMap[status] || statusClassMap.upcoming);
      statusEl.textContent = statusLabelMap[status] || statusLabelMap.upcoming;
    }
    if (joinBtn) {
      var meetingType = String(session.meeting_type || '').toLowerCase();
      var rawJoinUrl = (typeof session.join_url === 'string' && session.join_url !== '#' ? session.join_url : '') || (typeof session.meeting_info === 'string' ? session.meeting_info : '');
      var hasValidUrl = /^https?:\/\//i.test(rawJoinUrl);
      if (meetingType === 'zoom' && hasValidUrl) {
        joinBtn.classList.remove('hidden');
        joinBtn.classList.add('inline-flex');
        joinBtn.setAttribute('href', rawJoinUrl);
      } else {
        joinBtn.classList.add('hidden');
        joinBtn.classList.remove('inline-flex');
        joinBtn.removeAttribute('href');
      }
    }
  }
  function parseQueryState() {
    var url = new URL(window.location.href);
    var qs = url.searchParams;
    var view = qs.get('view') || 'week';
    var weekOffset = Number(qs.get('week_offset') || 0);
    var classId = Number(qs.get('class_id') || 0);
    var sessionId = qs.get('session_id') || selectedSessionId || '';
    return {
      view: view,
      week_offset: Number.isNaN(weekOffset) ? 0 : weekOffset,
      class_id: Number.isNaN(classId) ? 0 : classId,
      session_id: sessionId
    };
  }
  function updateUrlState(state) {
    var replace = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
    var url = new URL(window.location.href);
    var qs = url.searchParams;
    var setOrDelete = function setOrDelete(key, value) {
      var defaultValue = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
      if (String(value) === String(defaultValue) || value === '' || value === null || value === undefined) {
        qs["delete"](key);
        return;
      }
      qs.set(key, String(value));
    };
    setOrDelete('view', state.view, 'week');
    setOrDelete('week_offset', state.week_offset, 0);
    setOrDelete('class_id', state.class_id, 0);
    setOrDelete('session_id', state.session_id, '');
    var method = replace ? 'replaceState' : 'pushState';
    window.history[method]({}, '', url.toString());
  }
  function mapEvents(sessions) {
    var statusColorMap = {
      upcoming: '#3b82f6',
      live: '#10b981',
      completed: '#6b7280',
      missed: '#ef4444'
    };
    return (sessions || []).map(function (session) {
      var status = String(session.status || 'upcoming').toLowerCase();
      var color = statusColorMap[status] || statusColorMap.upcoming;
      return {
        id: session.id,
        title: session.class_name,
        start: session.start_local || session.start_iso,
        end: session.end_local || session.end_iso,
        backgroundColor: color,
        borderColor: color,
        extendedProps: session
      };
    });
  }
  function syncFormState(state) {
    if (!filterForm) return;
    var weekOffsetInput = filterForm.querySelector('input[name="week_offset"]');
    var viewSelect = filterForm.querySelector('select[name="view"]');
    var classSelect = filterForm.querySelector('select[name="class_id"]');
    if (weekOffsetInput) weekOffsetInput.value = String(state.week_offset);
    if (viewSelect) viewSelect.value = state.view;
    if (classSelect) classSelect.value = String(state.class_id);
  }
  function setLoading(isLoading) {
    if (loadingEl) loadingEl.classList.toggle('hidden', !isLoading);
    if (errorEl && isLoading) errorEl.classList.add('hidden');
    document.querySelectorAll('[data-cal-nav], [data-cal-view]').forEach(function (el) {
      el.toggleAttribute('disabled', isLoading);
      el.classList.toggle('opacity-60', isLoading);
      el.classList.toggle('cursor-not-allowed', isLoading);
    });
  }
  function applyPayload(payload, state) {
    var _payload$header, _payload$sessions_tot, _payload$upcoming_cou, _payload$live_count, _payload$missed_count;
    var sessions = payload.sessions || [];
    calendar.removeAllEvents();
    calendar.addEventSource(mapEvents(sessions));
    setActiveViewButton(calendar.view.type);
    syncFormState(state);
    if ((_payload$header = payload.header) !== null && _payload$header !== void 0 && _payload$header.week_start) {
      calendar.gotoDate(payload.header.week_start);
      var weekStartDate = new Date(payload.header.week_start);
      var weekEndDate = new Date(weekStartDate);
      weekEndDate.setDate(weekEndDate.getDate() + 6);
      formatDateRange(weekStartDate, weekEndDate);
    }
    if (statTotalEl) statTotalEl.textContent = String((_payload$sessions_tot = payload.sessions_total) !== null && _payload$sessions_tot !== void 0 ? _payload$sessions_tot : 0);
    if (statUpcomingEl) statUpcomingEl.textContent = String((_payload$upcoming_cou = payload.upcoming_count) !== null && _payload$upcoming_cou !== void 0 ? _payload$upcoming_cou : 0);
    if (statLiveEl) statLiveEl.textContent = String((_payload$live_count = payload.live_count) !== null && _payload$live_count !== void 0 ? _payload$live_count : 0);
    if (statMissedEl) statMissedEl.textContent = String((_payload$missed_count = payload.missed_count) !== null && _payload$missed_count !== void 0 ? _payload$missed_count : 0);
    updateDetail(payload.selected_session || sessions[0] || null);
  }
  function fetchAndRender(_x) {
    return _fetchAndRender.apply(this, arguments);
  }
  function _fetchAndRender() {
    _fetchAndRender = _asyncToGenerator(/*#__PURE__*/_regenerator().m(function _callee(state) {
      var replaceHistory,
        url,
        response,
        payload,
        _args = arguments,
        _t;
      return _regenerator().w(function (_context) {
        while (1) switch (_context.p = _context.n) {
          case 0:
            replaceHistory = _args.length > 1 && _args[1] !== undefined ? _args[1] : false;
            setLoading(true);
            _context.p = 1;
            url = new URL(dataUrl, window.location.origin);
            url.searchParams.set('view', state.view);
            if (state.week_offset !== 0) url.searchParams.set('week_offset', String(state.week_offset));
            if (state.class_id > 0) url.searchParams.set('class_id', String(state.class_id));
            if (state.session_id) url.searchParams.set('session_id', state.session_id);
            _context.n = 2;
            return fetch(url.toString(), {
              headers: {
                Accept: 'application/json'
              }
            });
          case 2:
            response = _context.v;
            if (response.ok) {
              _context.n = 3;
              break;
            }
            throw new Error('Không thể tải dữ liệu lịch học.');
          case 3:
            _context.n = 4;
            return response.json();
          case 4:
            payload = _context.v;
            applyPayload(payload, state);
            updateUrlState(state, replaceHistory);
            _context.n = 6;
            break;
          case 5:
            _context.p = 5;
            _t = _context.v;
            console.error(_t);
            if (errorEl) {
              errorEl.textContent = 'Không tải được dữ liệu lịch học. Vui lòng thử lại.';
              errorEl.classList.remove('hidden');
            }
          case 6:
            _context.p = 6;
            setLoading(false);
            return _context.f(6);
          case 7:
            return _context.a(2);
        }
      }, _callee, null, [[1, 5, 6, 7]]);
    }));
    return _fetchAndRender.apply(this, arguments);
  }
  var calendar = new Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/core'"); e.code = 'MODULE_NOT_FOUND'; throw e; }())(calendarEl, {
    plugins: [Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/daygrid'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()), Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/timegrid'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()), Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/list'"); e.code = 'MODULE_NOT_FOUND'; throw e; }()), Object(function webpackMissingModule() { var e = new Error("Cannot find module '@fullcalendar/interaction'"); e.code = 'MODULE_NOT_FOUND'; throw e; }())],
    initialView: initialView,
    initialDate: weekStart,
    events: initialEvents,
    timeZone: 'local',
    height: 'auto',
    headerToolbar: false,
    nowIndicator: true,
    allDaySlot: false,
    slotMinTime: '06:00:00',
    slotMaxTime: '22:00:00',
    slotDuration: '01:00:00',
    slotLabelInterval: '01:00:00',
    slotLabelFormat: {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    },
    eventTimeFormat: {
      hour: '2-digit',
      minute: '2-digit',
      hour12: false
    },
    eventClick: function eventClick(info) {
      updateDetail(info.event.extendedProps);
      var currentState = parseQueryState();
      currentState.session_id = info.event.id || '';
      updateUrlState(currentState, true);
    }
  });
  calendar.render();
  setActiveViewButton(calendar.view.type);
  if (weekStart) {
    var startDate = new Date(weekStart);
    var endDate = new Date(startDate);
    endDate.setDate(endDate.getDate() + 6);
    formatDateRange(startDate, endDate);
  }
  document.querySelectorAll('[data-cal-nav]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var state = parseQueryState();
      var action = btn.getAttribute('data-cal-nav');
      if (action === 'prev') {
        state.week_offset -= 1;
        state.session_id = '';
        fetchAndRender(state);
        return;
      }
      if (action === 'today') {
        state.week_offset = 0;
        state.session_id = '';
        fetchAndRender(state);
        return;
      }
      if (action === 'next') {
        state.week_offset += 1;
        state.session_id = '';
        fetchAndRender(state);
      }
    });
  });
  document.querySelectorAll('[data-cal-view]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var state = parseQueryState();
      var viewType = btn.getAttribute('data-cal-view');
      var viewMap = {
        timeGridDay: 'day',
        timeGridWeek: 'week',
        dayGridMonth: 'month',
        listWeek: 'list'
      };
      if (viewType) {
        calendar.changeView(viewType);
        state.view = viewMap[viewType] || 'week';
        state.session_id = '';
        fetchAndRender(state);
      }
    });
  });
  if (filterForm) {
    filterForm.addEventListener('submit', function (event) {
      event.preventDefault();
      var state = parseQueryState();
      var formData = new FormData(filterForm);
      state.view = String(formData.get('view') || 'week');
      state.class_id = Number(formData.get('class_id') || 0);
      state.week_offset = Number(formData.get('week_offset') || 0);
      state.session_id = '';
      fetchAndRender(state);
    });
  }
  if (initialEvents.length > 0) {
    var initialState = parseQueryState();
    syncFormState(initialState);
    var selectedInitial = initialEvents.find(function (eventItem) {
      return String(eventItem.id) === String(initialState.session_id);
    });
    updateDetail((selectedInitial || initialEvents[0]).extendedProps || null);
  } else {
    updateDetail(null);
  }
  window.addEventListener('popstate', function () {
    fetchAndRender(parseQueryState(), true);
  });
});
/******/ })()
;