import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'

import '@fullcalendar/daygrid/main.css'
import '@fullcalendar/timegrid/main.css'
import '@fullcalendar/list/main.css'

document.addEventListener('DOMContentLoaded', function () {
    const config = window.StudentScheduleConfig || {}
    const dataUrl = config.dataUrl || ''
    const root = document.getElementById('student-calendar-root')
    const calendarEl = document.getElementById('student-calendar')
    const filterForm = document.getElementById('schedule-filter-form')
    if (!root || !calendarEl || !dataUrl) return

    const initialEvents = JSON.parse(root.getAttribute('data-events') || '[]')
    const selectedSessionId = root.getAttribute('data-selected-session-id') || ''
    const initialView = root.getAttribute('data-initial-view') || 'timeGridWeek'
    const weekStart = root.getAttribute('data-week-start') || null
    const loadingEl = document.getElementById('schedule-calendar-loading')
    const errorEl = document.getElementById('schedule-calendar-error')
    const dateRangeEl = document.getElementById('schedule-date-range')

    const detailRoot = document.getElementById('schedule-session-detail')
    const detailContentEl = document.getElementById('schedule-detail-content')
    const detailEmptyEl = document.getElementById('schedule-detail-empty')
    const statusEl = document.getElementById('schedule-detail-status')
    const joinBtn = document.getElementById('schedule-detail-join-btn')
    const statTotalEl = document.getElementById('schedule-stat-total')
    const statUpcomingEl = document.getElementById('schedule-stat-upcoming')
    const statLiveEl = document.getElementById('schedule-stat-live')
    const statMissedEl = document.getElementById('schedule-stat-missed')

    const statusClassMap = {
        upcoming: 'bg-blue-500/10 text-blue-300 border-blue-500/30',
        live: 'bg-emerald-500/20 text-emerald-200 border-emerald-400/40',
        completed: 'bg-slate-500/10 text-slate-300 border-slate-500/30',
        missed: 'bg-red-500/10 text-red-300 border-red-500/30',
    }
    const statusLabelMap = {
        upcoming: 'Sắp diễn ra',
        live: 'Đang diễn ra',
        completed: 'Đã kết thúc',
        missed: 'Đã lỡ buổi',
    }

    function formatDateRange(start, end) {
        const format = (d) => {
            const dd = String(d.getDate()).padStart(2, '0')
            const mm = String(d.getMonth() + 1).padStart(2, '0')
            const yyyy = d.getFullYear()
            return `${dd}/${mm}/${yyyy}`
        }
        if (dateRangeEl) {
            dateRangeEl.textContent = `${format(start)} - ${format(end)}`
        }
    }

    function setActiveViewButton(viewType) {
        document.querySelectorAll('.schedule-view-btn').forEach((btn) => {
            const isActive = btn.getAttribute('data-cal-view') === viewType
            btn.classList.toggle('bg-emerald-500', isActive)
            btn.classList.toggle('text-white', isActive)
            btn.classList.toggle('text-slate-300', !isActive)
            btn.classList.toggle('hover:bg-slate-700', !isActive)
        })
    }

    function updateDetail(session) {
        if (!detailRoot) return
        if (!session) {
            if (detailContentEl) detailContentEl.classList.add('hidden')
            if (detailEmptyEl) detailEmptyEl.classList.remove('hidden')
            if (joinBtn) {
                joinBtn.classList.add('hidden')
                joinBtn.removeAttribute('href')
            }
            return
        }
        if (detailContentEl) detailContentEl.classList.remove('hidden')
        if (detailEmptyEl) detailEmptyEl.classList.add('hidden')

        ;['class_name', 'course', 'teacher', 'start_at', 'meeting_info', 'description'].forEach((key) => {
            const el = detailRoot.querySelector(`[data-detail="${key}"]`)
            if (el) el.textContent = session[key] || ''
        })

        if (statusEl) {
            const status = (session.status || 'upcoming').toLowerCase()
            statusEl.className = `inline-flex items-center px-3 py-1 rounded-full border text-xs uppercase font-semibold ${statusClassMap[status] || statusClassMap.upcoming}`
            statusEl.textContent = statusLabelMap[status] || statusLabelMap.upcoming
        }

        if (joinBtn) {
            const meetingType = String(session.meeting_type || '').toLowerCase()
            const rawJoinUrl =
                (typeof session.join_url === 'string' && session.join_url !== '#' ? session.join_url : '') ||
                (typeof session.meeting_info === 'string' ? session.meeting_info : '')
            const hasValidUrl = /^https?:\/\//i.test(rawJoinUrl)

            if (meetingType === 'zoom' && hasValidUrl) {
                joinBtn.classList.remove('hidden')
                joinBtn.classList.add('inline-flex')
                joinBtn.setAttribute('href', rawJoinUrl)
            } else {
                joinBtn.classList.add('hidden')
                joinBtn.classList.remove('inline-flex')
                joinBtn.removeAttribute('href')
            }
        }
    }

    function parseQueryState() {
        const url = new URL(window.location.href)
        const qs = url.searchParams
        const view = qs.get('view') || 'week'
        const weekOffset = Number(qs.get('week_offset') || 0)
        const classId = Number(qs.get('class_id') || 0)
        const sessionId = qs.get('session_id') || selectedSessionId || ''

        return {
            view,
            week_offset: Number.isNaN(weekOffset) ? 0 : weekOffset,
            class_id: Number.isNaN(classId) ? 0 : classId,
            session_id: sessionId,
        }
    }

    function updateUrlState(state, replace = false) {
        const url = new URL(window.location.href)
        const qs = url.searchParams
        const setOrDelete = (key, value, defaultValue = '') => {
            if (String(value) === String(defaultValue) || value === '' || value === null || value === undefined) {
                qs.delete(key)
                return
            }
            qs.set(key, String(value))
        }

        setOrDelete('view', state.view, 'week')
        setOrDelete('week_offset', state.week_offset, 0)
        setOrDelete('class_id', state.class_id, 0)
        setOrDelete('session_id', state.session_id, '')

        const method = replace ? 'replaceState' : 'pushState'
        window.history[method]({}, '', url.toString())
    }

    function mapEvents(sessions) {
        const statusColorMap = {
            upcoming: '#3b82f6',
            live: '#10b981',
            completed: '#6b7280',
            missed: '#ef4444',
        }

        return (sessions || []).map((session) => {
            const status = String(session.status || 'upcoming').toLowerCase()
            const color = statusColorMap[status] || statusColorMap.upcoming

            return {
                id: session.id,
                title: session.class_name,
                start: session.start_local || session.start_iso,
                end: session.end_local || session.end_iso,
                backgroundColor: color,
                borderColor: color,
                extendedProps: session,
            }
        })
    }

    function syncFormState(state) {
        if (!filterForm) return
        const weekOffsetInput = filterForm.querySelector('input[name="week_offset"]')
        const viewSelect = filterForm.querySelector('select[name="view"]')
        const classSelect = filterForm.querySelector('select[name="class_id"]')
        if (weekOffsetInput) weekOffsetInput.value = String(state.week_offset)
        if (viewSelect) viewSelect.value = state.view
        if (classSelect) classSelect.value = String(state.class_id)
    }

    function setLoading(isLoading) {
        if (loadingEl) loadingEl.classList.toggle('hidden', !isLoading)
        if (errorEl && isLoading) errorEl.classList.add('hidden')
        document.querySelectorAll('[data-cal-nav], [data-cal-view]').forEach((el) => {
            el.toggleAttribute('disabled', isLoading)
            el.classList.toggle('opacity-60', isLoading)
            el.classList.toggle('cursor-not-allowed', isLoading)
        })
    }

    function applyPayload(payload, state) {
        const sessions = payload.sessions || []
        calendar.removeAllEvents()
        calendar.addEventSource(mapEvents(sessions))
        setActiveViewButton(calendar.view.type)
        syncFormState(state)

        if (payload.header?.week_start) {
            calendar.gotoDate(payload.header.week_start)
            const weekStartDate = new Date(payload.header.week_start)
            const weekEndDate = new Date(weekStartDate)
            weekEndDate.setDate(weekEndDate.getDate() + 6)
            formatDateRange(weekStartDate, weekEndDate)
        }

        if (statTotalEl) statTotalEl.textContent = String(payload.sessions_total ?? 0)
        if (statUpcomingEl) statUpcomingEl.textContent = String(payload.upcoming_count ?? 0)
        if (statLiveEl) statLiveEl.textContent = String(payload.live_count ?? 0)
        if (statMissedEl) statMissedEl.textContent = String(payload.missed_count ?? 0)

        updateDetail(payload.selected_session || sessions[0] || null)
    }

    async function fetchAndRender(state, replaceHistory = false) {
        setLoading(true)
        try {
            const url = new URL(dataUrl, window.location.origin)
            url.searchParams.set('view', state.view)
            if (state.week_offset !== 0) url.searchParams.set('week_offset', String(state.week_offset))
            if (state.class_id > 0) url.searchParams.set('class_id', String(state.class_id))
            if (state.session_id) url.searchParams.set('session_id', state.session_id)

            const response = await fetch(url.toString(), {
                headers: { Accept: 'application/json' },
            })
            if (!response.ok) {
                throw new Error('Không thể tải dữ liệu lịch học.')
            }
            const payload = await response.json()
            applyPayload(payload, state)
            updateUrlState(state, replaceHistory)
        } catch (error) {
            console.error(error)
            if (errorEl) {
                errorEl.textContent = 'Không tải được dữ liệu lịch học. Vui lòng thử lại.'
                errorEl.classList.remove('hidden')
            }
        } finally {
            setLoading(false)
        }
    }

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView,
        initialDate: weekStart,
        events: initialEvents,
        firstDay: 1,
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
            hour12: false,
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },
        eventClick: function (info) {
            updateDetail(info.event.extendedProps)
            const currentState = parseQueryState()
            currentState.session_id = info.event.id || ''
            updateUrlState(currentState, true)
        },
    })

    calendar.render()
    setActiveViewButton(calendar.view.type)
    if (weekStart) {
        const startDate = new Date(weekStart)
        const endDate = new Date(startDate)
        endDate.setDate(endDate.getDate() + 6)
        formatDateRange(startDate, endDate)
    }

    document.querySelectorAll('[data-cal-nav]').forEach((btn) => {
        btn.addEventListener('click', function () {
            const state = parseQueryState()
            const action = btn.getAttribute('data-cal-nav')
            if (action === 'prev') {
                state.week_offset -= 1
                state.session_id = ''
                fetchAndRender(state)
                return
            }
            if (action === 'today') {
                state.week_offset = 0
                state.session_id = ''
                fetchAndRender(state)
                return
            }
            if (action === 'next') {
                state.week_offset += 1
                state.session_id = ''
                fetchAndRender(state)
            }
        })
    })

    document.querySelectorAll('[data-cal-view]').forEach((btn) => {
        btn.addEventListener('click', function () {
            const state = parseQueryState()
            const viewType = btn.getAttribute('data-cal-view')
            const viewMap = {
                timeGridDay: 'day',
                timeGridWeek: 'week',
                dayGridMonth: 'month',
                listWeek: 'list',
            }
            if (viewType) {
                calendar.changeView(viewType)
                state.view = viewMap[viewType] || 'week'
                state.session_id = ''
                fetchAndRender(state)
            }
        })
    })

    if (filterForm) {
        filterForm.addEventListener('submit', function (event) {
            event.preventDefault()
            const state = parseQueryState()
            const formData = new FormData(filterForm)
            state.view = String(formData.get('view') || 'week')
            state.class_id = Number(formData.get('class_id') || 0)
            state.week_offset = Number(formData.get('week_offset') || 0)
            state.session_id = ''
            fetchAndRender(state)
        })
    }

    if (initialEvents.length > 0) {
        const initialState = parseQueryState()
        syncFormState(initialState)
        const selectedInitial = initialEvents.find((eventItem) => String(eventItem.id) === String(initialState.session_id))
        updateDetail((selectedInitial || initialEvents[0]).extendedProps || null)
    } else {
        updateDetail(null)
    }

    window.addEventListener('popstate', function () {
        fetchAndRender(parseQueryState(), true)
    })
})

