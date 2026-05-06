import { Calendar } from '@fullcalendar/core'
import dayGridPlugin from '@fullcalendar/daygrid'
import timeGridPlugin from '@fullcalendar/timegrid'
import listPlugin from '@fullcalendar/list'
import interactionPlugin from '@fullcalendar/interaction'

import '@fullcalendar/daygrid/main.css'
import '@fullcalendar/timegrid/main.css'
import '@fullcalendar/list/main.css'

document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('student-calendar-root')
    const calendarEl = document.getElementById('student-calendar')
    if (!root || !calendarEl) return

    const events = JSON.parse(root.getAttribute('data-events') || '[]')
    const initialView = root.getAttribute('data-initial-view') || 'timeGridWeek'
    const dateRangeEl = document.getElementById('schedule-date-range')

    const detailRoot = document.getElementById('schedule-session-detail')
    const statusEl = document.getElementById('schedule-detail-status')
    const joinBtn = document.getElementById('schedule-detail-join-btn')

    const statusClassMap = {
        upcoming: 'bg-blue-500/10 text-blue-300 border-blue-500/30',
        live: 'bg-emerald-500/20 text-emerald-200 border-emerald-400/40',
        completed: 'bg-slate-500/10 text-slate-300 border-slate-500/30',
        missed: 'bg-red-500/10 text-red-300 border-red-500/30',
    }

    function formatRange(calendarApi) {
        const view = calendarApi.view
        const start = view.activeStart
        const end = new Date(view.activeEnd.getTime() - 24 * 60 * 60 * 1000)

        const format = (d) => {
            const dd = String(d.getDate()).padStart(2, '0')
            const mm = String(d.getMonth() + 1).padStart(2, '0')
            const yyyy = d.getFullYear()
            return `${dd}/${mm}/${yyyy}`
        }

        if (dateRangeEl) dateRangeEl.textContent = `${format(start)} - ${format(end)}`
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
        if (!detailRoot || !session) return
        ;['class_name', 'course', 'teacher', 'start_at', 'meeting_info', 'description'].forEach((key) => {
            const el = detailRoot.querySelector(`[data-detail="${key}"]`)
            if (el) el.textContent = session[key] || ''
        })

        if (statusEl) {
            const status = (session.status || 'upcoming').toLowerCase()
            statusEl.className = `inline-flex items-center px-2 py-1 rounded-full border text-[10px] uppercase font-semibold ${statusClassMap[status] || statusClassMap.upcoming}`
            statusEl.textContent = status
        }

        if (joinBtn) {
            if (session.status === 'completed' || session.status === 'missed') {
                joinBtn.classList.add('opacity-60', 'cursor-not-allowed')
                joinBtn.setAttribute('disabled', 'disabled')
            } else {
                joinBtn.classList.remove('opacity-60', 'cursor-not-allowed')
                joinBtn.removeAttribute('disabled')
            }
        }
    }

    const calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin],
        initialView,
        events,
        height: 'auto',
        headerToolbar: false,
        nowIndicator: true,
        allDaySlot: false,
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false,
        },
        eventClick: function (info) {
            updateDetail(info.event.extendedProps)
        },
        datesSet: function () {
            formatRange(calendar)
            setActiveViewButton(calendar.view.type)
        },
    })

    calendar.render()
    formatRange(calendar)
    setActiveViewButton(calendar.view.type)

    document.querySelectorAll('[data-cal-nav]').forEach((btn) => {
        btn.addEventListener('click', function () {
            const action = btn.getAttribute('data-cal-nav')
            if (action === 'prev') calendar.prev()
            if (action === 'today') calendar.today()
            if (action === 'next') calendar.next()
        })
    })

    document.querySelectorAll('[data-cal-view]').forEach((btn) => {
        btn.addEventListener('click', function () {
            const viewType = btn.getAttribute('data-cal-view')
            if (viewType) calendar.changeView(viewType)
        })
    })

    if (events.length > 0) {
        updateDetail(events[0].extendedProps)
    }
})

