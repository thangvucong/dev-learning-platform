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
    const attendanceBaseUrl = config.attendanceBaseUrl || ''
    const assignmentBaseUrl = config.assignmentBaseUrl || attendanceBaseUrl
    const assignmentMode = config.assignmentMode || 'teacher'
    const assignmentSubmissionBaseUrl = config.assignmentSubmissionBaseUrl || ''
    const assignmentSubmissionsBaseUrl = config.assignmentSubmissionsBaseUrl || ''
    const assignmentGradeBaseUrl = config.assignmentGradeBaseUrl || ''
    const classSessionBaseUrl = config.classSessionBaseUrl || ''
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
    const attendancePanel = document.getElementById('schedule-attendance-panel')
    const attendanceSummaryEl = attendancePanel ? attendancePanel.querySelector('[data-attendance-summary]') : null
    const attendanceListEl = attendancePanel ? attendancePanel.querySelector('[data-attendance-list]') : null
    const attendanceMessageEl = attendancePanel ? attendancePanel.querySelector('[data-attendance-message]') : null
    const attendanceSearchEl = document.getElementById('schedule-attendance-search')
    const attendanceRefreshBtn = attendancePanel ? attendancePanel.querySelector('[data-attendance-refresh]') : null
    const assignmentsPanel = document.getElementById('schedule-assignments-panel')
    const assignmentSummaryEl = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-summary]') : null
    const assignmentListEl = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-list]') : null
    const assignmentMessageEl = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-message]') : null
    const assignmentRefreshBtn = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-refresh]') : null
    const assignmentFormShell = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-form-shell]') : null
    const assignmentFormToggle = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-form-toggle]') : null
    const assignmentFormToggleLabel = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-form-toggle-label]') : null
    const assignmentFormToggleIcon = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-form-toggle-icon]') : null
    const assignmentForm = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-form]') : null
    const assignmentSubmitBtn = assignmentsPanel ? assignmentsPanel.querySelector('[data-assignment-submit]') : null
    const assignmentSubmissionsPanel = document.getElementById('assignment-submissions-modal')
    const assignmentSubmissionsTitleEl = assignmentSubmissionsPanel ? assignmentSubmissionsPanel.querySelector('[data-assignment-submissions-title]') : null
    const assignmentSubmissionsSummaryEl = assignmentSubmissionsPanel ? assignmentSubmissionsPanel.querySelector('[data-assignment-submissions-summary]') : null
    const assignmentSubmissionsListEl = assignmentSubmissionsPanel ? assignmentSubmissionsPanel.querySelector('[data-assignment-submissions-list]') : null
    const assignmentSubmissionsMessageEl = assignmentSubmissionsPanel ? assignmentSubmissionsPanel.querySelector('[data-assignment-submissions-message]') : null
    const assignmentSubmissionsCloseBtn = assignmentSubmissionsPanel ? assignmentSubmissionsPanel.querySelector('[data-assignment-submissions-close]') : null
    const assignmentSubmissionSearchEl = document.getElementById('schedule-assignment-submission-search')
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
    let attendanceState = {
        sessionId: null,
        students: [],
        summary: null,
        query: '',
        filter: 'all',
        loading: false,
    }
    let assignmentState = {
        sessionId: null,
        assignments: [],
        summary: null,
        loading: false,
    }
    let assignmentSubmissionState = {
        assignmentId: null,
        submissions: [],
        summary: null,
        query: '',
        filter: 'all',
        loading: false,
    }
    const ensureSessionRequests = {}
    let activeSessionPanel = 'detail'

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
    const attendanceStatusMap = {
        present: { label: 'Có mặt', dot: 'bg-emerald-400', text: 'text-emerald-300', border: 'border-emerald-500/40', bg: 'bg-emerald-500/10' },
        late: { label: 'Trễ', dot: 'bg-amber-400', text: 'text-amber-300', border: 'border-amber-500/40', bg: 'bg-amber-500/10' },
        absent: { label: 'Vắng', dot: 'bg-red-400', text: 'text-red-300', border: 'border-red-500/40', bg: 'bg-red-500/10' },
        excused: { label: 'Có phép', dot: 'bg-sky-400', text: 'text-sky-300', border: 'border-sky-500/40', bg: 'bg-sky-500/10' },
        unmarked: { label: 'Chưa điểm danh', dot: 'bg-slate-500', text: 'text-slate-400', border: 'border-slate-600', bg: 'bg-slate-800/40' },
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
            resetAttendancePanel()
            resetAssignmentsPanel()
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

        loadAttendanceForSession(session)
        loadAssignmentsForSession(session)
    }

    function activateSessionPanel(target) {
        activeSessionPanel = target || 'detail'
        document.querySelectorAll('[data-session-tab]').forEach((btn) => {
            const active = btn.getAttribute('data-session-tab') === activeSessionPanel
            btn.classList.toggle('bg-emerald-500', active)
            btn.classList.toggle('text-white', active)
            btn.classList.toggle('text-slate-300', !active)
            btn.classList.toggle('hover:bg-slate-700', !active)
        })

        document.querySelectorAll('[data-session-panel]').forEach((panel) => {
            panel.classList.toggle('hidden', panel.getAttribute('data-session-panel') !== activeSessionPanel)
        })
    }

    function setAttendanceMessage(message, tone = 'muted') {
        if (!attendanceMessageEl) return
        attendanceMessageEl.textContent = message
        attendanceMessageEl.classList.remove('hidden', 'border-red-500/30', 'text-red-300', 'border-slate-700', 'text-slate-300')
        attendanceMessageEl.classList.add(tone === 'error' ? 'border-red-500/30' : 'border-slate-700')
        attendanceMessageEl.classList.add(tone === 'error' ? 'text-red-300' : 'text-slate-300')
    }

    function clearAttendanceMessage() {
        if (!attendanceMessageEl) return
        attendanceMessageEl.textContent = ''
        attendanceMessageEl.classList.add('hidden')
    }

    function resetAttendancePanel() {
        attendanceState = {
            sessionId: null,
            students: [],
            summary: null,
            query: attendanceSearchEl ? attendanceSearchEl.value.trim().toLowerCase() : '',
            filter: attendanceState.filter || 'all',
            loading: false,
        }
        if (attendancePanel) attendancePanel.setAttribute('data-session-id', '')
        if (attendanceSummaryEl) attendanceSummaryEl.textContent = 'Chọn buổi học để tải danh sách.'
        if (attendanceListEl) attendanceListEl.innerHTML = ''
        setAttendanceMessage('Chọn một buổi học có lịch chi tiết để điểm danh.')
    }

    function attendanceUrl(sessionId, studentId = null) {
        if (!attendanceBaseUrl || !sessionId) return ''
        return `${attendanceBaseUrl}/${sessionId}/attendance${studentId ? `/${studentId}` : ''}`
    }

    function assignmentUrl(sessionId) {
        if (!assignmentBaseUrl || !sessionId) return ''
        return `${assignmentBaseUrl}/${sessionId}/assignments`
    }

    function ensureSessionUrl(classId) {
        if (!classSessionBaseUrl || !classId) return ''
        return `${classSessionBaseUrl}/${classId}/sessions`
    }

    async function loadAttendanceForSession(session) {
        if (!attendancePanel) return
        let sessionId = session && session.session_id ? Number(session.session_id) : 0

        if (!sessionId && session && session.class_id) {
            sessionId = await ensureAttendanceSession(session.class_id)
            if (sessionId) {
                session.session_id = sessionId
            }
        }

        attendanceState.sessionId = sessionId || null
        attendanceState.students = []
        attendanceState.summary = null
        attendancePanel.setAttribute('data-session-id', sessionId ? String(sessionId) : '')

        if (!sessionId) {
            resetAttendancePanel()
            return
        }

        attendanceState.loading = true
        if (attendanceSummaryEl) attendanceSummaryEl.textContent = 'Đang tải danh sách học viên...'
        if (attendanceListEl) attendanceListEl.innerHTML = ''
        clearAttendanceMessage()

        try {
            const response = await fetch(attendanceUrl(sessionId), {
                headers: { Accept: 'application/json' },
            })
            if (!response.ok) throw new Error('Không tải được danh sách điểm danh.')
            const payload = await response.json()
            applyAttendancePayload(payload)
        } catch (error) {
            console.error(error)
            attendanceState.students = []
            attendanceState.summary = null
            if (attendanceSummaryEl) attendanceSummaryEl.textContent = 'Không tải được điểm danh.'
            setAttendanceMessage('Không tải được danh sách học viên. Vui lòng thử lại.', 'error')
        } finally {
            attendanceState.loading = false
        }
    }

    async function ensureAttendanceSession(classId) {
        const url = ensureSessionUrl(classId)
        if (!url) return 0

        if (ensureSessionRequests[classId]) {
            return ensureSessionRequests[classId]
        }

        setAttendanceMessage('Đang chuẩn bị buổi điểm danh...')

        ensureSessionRequests[classId] = (async function () {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({}),
            })
            if (!response.ok) throw new Error('Không tạo được buổi điểm danh.')
            const payload = await response.json()
            if (payload.attendance) {
                applyAttendancePayload(payload.attendance)
            }

            return Number(payload.session_id || payload.attendance?.session?.id || 0)
        })()

        try {
            return await ensureSessionRequests[classId]
        } catch (error) {
            console.error(error)
            setAttendanceMessage('Buổi này chưa có dữ liệu session để điểm danh và không thể tự tạo. Vui lòng thử lại.', 'error')
            return 0
        } finally {
            delete ensureSessionRequests[classId]
        }
    }

    function applyAttendancePayload(payload) {
        attendanceState.sessionId = payload.session?.id || attendanceState.sessionId
        attendanceState.students = payload.students || []
        attendanceState.summary = payload.summary || null
        attendanceState.query = attendanceSearchEl ? attendanceSearchEl.value.trim().toLowerCase() : ''

        updateAttendanceSummary()
        renderAttendanceList()
        clearAttendanceMessage()
    }

    function updateAttendanceSummary() {
        if (!attendanceSummaryEl) return
        const summary = attendanceState.summary
        if (!summary) {
            attendanceSummaryEl.textContent = 'Chưa có dữ liệu điểm danh.'
            return
        }
        attendanceSummaryEl.textContent = `${summary.marked}/${summary.total} đã điểm danh · ${summary.rate}% có mặt`

        const teacherEl = detailRoot ? detailRoot.querySelector('[data-detail="teacher"]') : null
        if (teacherEl) teacherEl.textContent = `Điểm danh ${summary.marked}/${summary.total}`
    }

    function currentAttendanceStudents() {
        const query = attendanceState.query
        const filter = attendanceState.filter

        return attendanceState.students.filter((student) => {
            const status = student.status || 'unmarked'
            const matchesFilter = filter === 'all' || status === filter
            const haystack = `${student.name || ''} ${student.email || ''}`.toLowerCase()
            const matchesQuery = !query || haystack.includes(query)

            return matchesFilter && matchesQuery
        })
    }

    function renderAttendanceList() {
        if (!attendanceListEl) return
        attendanceListEl.innerHTML = ''

        const students = currentAttendanceStudents()
        if (students.length === 0) {
            setAttendanceMessage(attendanceState.students.length === 0 ? 'Lớp chưa có học viên.' : 'Không tìm thấy học viên phù hợp.')
            return
        }

        clearAttendanceMessage()
        students.forEach((student) => {
            attendanceListEl.appendChild(createAttendanceRow(student))
        })
    }

    function createAttendanceRow(student) {
        const status = student.status || 'unmarked'
        const statusMeta = attendanceStatusMap[status] || attendanceStatusMap.unmarked
        const row = document.createElement('div')
        row.className = 'rounded-xl border border-slate-700 bg-slate-900/45 p-3'

        const top = document.createElement('div')
        top.className = 'flex items-center justify-between gap-3'

        const info = document.createElement('div')
        info.className = 'min-w-0'

        const name = document.createElement('p')
        name.className = 'truncate text-sm font-semibold text-white'
        name.textContent = student.name || 'Học viên'

        const email = document.createElement('p')
        email.className = 'mt-1 truncate text-xs text-slate-400'
        email.textContent = student.email || ''

        const pill = document.createElement('span')
        pill.className = `mt-1.5 inline-flex items-center rounded-full border px-2 py-0.5 text-[11px] font-semibold ${statusMeta.border} ${statusMeta.bg} ${statusMeta.text}`
        const dot = document.createElement('span')
        dot.className = `mr-1.5 h-2 w-2 rounded-full ${statusMeta.dot}`
        pill.appendChild(dot)
        pill.append(document.createTextNode(statusMeta.label))

        info.appendChild(name)
        if (student.email) info.appendChild(email)
        info.appendChild(pill)
        top.appendChild(info)

        const actions = document.createElement('div')
        actions.className = 'grid shrink-0 grid-cols-4 gap-1.5'

            ;['present', 'late', 'absent', 'excused'].forEach((option) => {
                actions.appendChild(createStatusButton(student, option))
            })

        top.appendChild(actions)
        row.appendChild(top)
        return row
    }

    function createStatusButton(student, status) {
        const meta = attendanceStatusMap[status]
        const active = (student.status || 'unmarked') === status
        const button = document.createElement('button')
        button.type = 'button'
        button.className = `attendance-status-dot ${active ? `${meta.border} ${meta.bg}` : 'border-slate-700 bg-slate-900/60 hover:bg-slate-800'}`
        button.setAttribute('title', meta.label)
        button.setAttribute('aria-label', `${meta.label} - ${student.name || 'Học viên'}`)
        button.dataset.studentId = String(student.id)
        button.dataset.status = status

        const dot = document.createElement('span')
        dot.className = `h-2.5 w-2.5 rounded-full ${meta.dot}`
        button.appendChild(dot)

        button.addEventListener('click', function () {
            updateAttendanceStatus(student.id, status)
        })

        return button
    }

    async function updateAttendanceStatus(studentId, status) {
        if (!attendanceState.sessionId) return
        await submitAttendance(attendanceUrl(attendanceState.sessionId, studentId), status)
    }

    async function bulkUpdateAttendance(status) {
        if (!attendanceState.sessionId) return
        await submitAttendance(attendanceUrl(attendanceState.sessionId), status)
    }

    async function submitAttendance(url, status) {
        if (!url) return
        setAttendanceMessage('Đang cập nhật điểm danh...')
        try {
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ status }),
            })
            if (!response.ok) throw new Error('Không cập nhật được điểm danh.')
            const payload = await response.json()
            applyAttendancePayload(payload)
        } catch (error) {
            console.error(error)
            setAttendanceMessage('Không cập nhật được điểm danh. Vui lòng thử lại.', 'error')
        }
    }

    function setAssignmentMessage(message, tone = 'muted') {
        if (!assignmentMessageEl) return
        assignmentMessageEl.textContent = message
        assignmentMessageEl.classList.remove('hidden', 'border-red-500/30', 'text-red-300', 'border-slate-700', 'text-slate-300')
        assignmentMessageEl.classList.add(tone === 'error' ? 'border-red-500/30' : 'border-slate-700')
        assignmentMessageEl.classList.add(tone === 'error' ? 'text-red-300' : 'text-slate-300')
    }

    function clearAssignmentMessage() {
        if (!assignmentMessageEl) return
        assignmentMessageEl.textContent = ''
        assignmentMessageEl.classList.add('hidden')
    }

    function showToast(type, message) {
        if (window.flasher && typeof window.flasher[type] === 'function') {
            window.flasher[type](message)
            return
        }

        if (window.toastr && typeof window.toastr[type] === 'function') {
            window.toastr[type](message)
            return
        }

        let container = document.getElementById('schedule-toast-container')
        if (!container) {
            container = document.createElement('div')
            container.id = 'schedule-toast-container'
            container.className = 'fixed right-4 top-4 z-[9999] space-y-2'
            document.body.appendChild(container)
        }

        const toast = document.createElement('div')
        toast.className = `min-w-[260px] rounded-xl border px-4 py-3 text-sm font-semibold shadow-lg ${type === 'error' ? 'border-red-500/40 bg-red-950 text-red-100' : 'border-emerald-500/40 bg-emerald-950 text-emerald-100'}`
        toast.textContent = message
        container.appendChild(toast)
        window.setTimeout(function () {
            toast.remove()
            if (container && container.childElementCount === 0) {
                container.remove()
            }
        }, 3500)
    }

    function assignmentErrorMessage(payload, fallback) {
        if (payload?.errors) {
            const firstError = Object.values(payload.errors).flat()[0]
            if (firstError) return firstError
        }

        return payload?.message || fallback
    }

    function setAssignmentFormVisible(visible) {
        if (!assignmentFormShell) return
        assignmentFormShell.classList.toggle('hidden', !visible)
        if (visible) hideAssignmentSubmissionsPanel()
        if (assignmentFormToggle) {
            assignmentFormToggle.setAttribute('aria-expanded', visible ? 'true' : 'false')
        }
        if (assignmentFormToggleLabel) {
            assignmentFormToggleLabel.textContent = visible ? 'Ẩn form' : 'Tạo bài'
        }
        if (assignmentFormToggleIcon) {
            assignmentFormToggleIcon.classList.toggle('rotate-180', visible)
        }
        if (visible && assignmentForm) {
            assignmentForm.querySelector('input[name="title"]')?.focus()
        }
    }

    function setAssignmentSubmissionsMessage(message, tone = 'muted') {
        if (!assignmentSubmissionsMessageEl) return
        assignmentSubmissionsMessageEl.textContent = message
        assignmentSubmissionsMessageEl.classList.remove('hidden', 'border-red-500/30', 'text-red-300', 'border-slate-700', 'text-slate-300')
        assignmentSubmissionsMessageEl.classList.add(tone === 'error' ? 'border-red-500/30' : 'border-slate-700')
        assignmentSubmissionsMessageEl.classList.add(tone === 'error' ? 'text-red-300' : 'text-slate-300')
    }

    function clearAssignmentSubmissionsMessage() {
        if (!assignmentSubmissionsMessageEl) return
        assignmentSubmissionsMessageEl.textContent = ''
        assignmentSubmissionsMessageEl.classList.add('hidden')
    }

    function hideAssignmentSubmissionsPanel() {
        if (!assignmentSubmissionsPanel) return
        assignmentSubmissionsPanel.classList.add('hidden')
        document.body.classList.remove('overflow-hidden')
    }

    function showAssignmentSubmissionsPanel() {
        if (!assignmentSubmissionsPanel) return
        assignmentSubmissionsPanel.classList.remove('hidden')
        document.body.classList.add('overflow-hidden')
    }

    function resetAssignmentSubmissionsPanel() {
        assignmentSubmissionState = {
            assignmentId: null,
            submissions: [],
            summary: null,
            query: assignmentSubmissionSearchEl ? assignmentSubmissionSearchEl.value.trim().toLowerCase() : '',
            filter: assignmentSubmissionState.filter || 'all',
            loading: false,
        }
        if (assignmentSubmissionsTitleEl) assignmentSubmissionsTitleEl.textContent = 'Bài nộp'
        if (assignmentSubmissionsSummaryEl) assignmentSubmissionsSummaryEl.textContent = 'Chọn bài tập để xem bài nộp.'
        if (assignmentSubmissionsListEl) assignmentSubmissionsListEl.innerHTML = ''
        clearAssignmentSubmissionsMessage()
        hideAssignmentSubmissionsPanel()
    }

    function resetAssignmentsPanel() {
        assignmentState = {
            sessionId: null,
            assignments: [],
            summary: null,
            loading: false,
        }
        if (assignmentsPanel) assignmentsPanel.setAttribute('data-session-id', '')
        if (assignmentSummaryEl) assignmentSummaryEl.textContent = 'Chọn buổi học để tải bài tập.'
        if (assignmentListEl) assignmentListEl.innerHTML = ''
        setAssignmentFormVisible(false)
        resetAssignmentSubmissionsPanel()
        setAssignmentMessage(`Chọn một buổi học có lịch chi tiết để ${assignmentMode === 'student' ? 'xem bài tập' : 'giao bài tập'}.`)
    }

    async function loadAssignmentsForSession(session) {
        if (!assignmentsPanel) return
        let sessionId = session && session.session_id ? Number(session.session_id) : 0

        if (assignmentMode !== 'student' && !sessionId && session && session.class_id) {
            sessionId = await ensureAttendanceSession(session.class_id)
            if (sessionId) {
                session.session_id = sessionId
            }
        }

        assignmentState.sessionId = sessionId || null
        assignmentState.assignments = []
        assignmentState.summary = null
        assignmentsPanel.setAttribute('data-session-id', sessionId ? String(sessionId) : '')
        if (assignmentMode === 'teacher' && assignmentForm) {
            assignmentForm.setAttribute('action', sessionId ? assignmentUrl(sessionId) : '#')
        }

        if (!sessionId) {
            resetAssignmentsPanel()
            setAssignmentMessage('Buổi học này chưa có dữ liệu bài tập.')
            return
        }

        assignmentState.loading = true
        if (assignmentSummaryEl) assignmentSummaryEl.textContent = 'Đang tải bài tập...'
        if (assignmentListEl) assignmentListEl.innerHTML = ''
        clearAssignmentMessage()

        try {
            const response = await fetch(assignmentUrl(sessionId), {
                headers: { Accept: 'application/json' },
            })
            if (!response.ok) throw new Error('Không tải được bài tập.')
            const payload = await response.json()
            applyAssignmentPayload(payload)
        } catch (error) {
            console.error(error)
            assignmentState.assignments = []
            assignmentState.summary = null
            if (assignmentSummaryEl) assignmentSummaryEl.textContent = 'Không tải được bài tập.'
            setAssignmentMessage('Không tải được danh sách bài tập. Vui lòng thử lại.', 'error')
        } finally {
            assignmentState.loading = false
        }
    }

    function applyAssignmentPayload(payload) {
        assignmentState.sessionId = payload.session?.id || assignmentState.sessionId
        assignmentState.assignments = payload.assignments || []
        assignmentState.summary = payload.summary || null
        updateAssignmentSummary()
        clearAssignmentMessage()
        renderAssignmentList()
    }

    function updateAssignmentSummary() {
        if (!assignmentSummaryEl) return
        const summary = assignmentState.summary
        if (!summary) {
            assignmentSummaryEl.textContent = 'Chưa có dữ liệu bài tập.'
            return
        }
        if (assignmentMode === 'student') {
            assignmentSummaryEl.textContent = `${summary.submitted || 0}/${summary.total || 0} bài đã nộp`
            return
        }

        assignmentSummaryEl.textContent = `${summary.total} bài tập · ${summary.published} đã giao · ${summary.draft} nháp`
    }

    function renderAssignmentList() {
        if (!assignmentListEl) return
        assignmentListEl.innerHTML = ''

        if (assignmentState.assignments.length === 0) {
            setAssignmentMessage('Buổi học này chưa có bài tập.')
            return
        }

        clearAssignmentMessage()
        assignmentState.assignments.forEach((assignment) => {
            assignmentListEl.appendChild(assignmentMode === 'student'
                ? createStudentAssignmentRow(assignment)
                : createAssignmentRow(assignment))
        })
    }

    function createAssignmentRow(assignment) {
        const row = document.createElement('div')
        row.className = 'rounded-xl border border-slate-700 bg-slate-900/45 p-3'

        const top = document.createElement('div')
        top.className = 'flex items-start justify-between gap-3'

        const info = document.createElement('div')
        info.className = 'min-w-0'

        const title = document.createElement('p')
        title.className = 'text-sm font-semibold text-white'
        title.textContent = assignment.title || 'Bài tập'

        const meta = document.createElement('p')
        meta.className = 'mt-1 text-xs text-slate-400'
        const dueText = assignment.due_at ? `Hạn ${assignment.due_at}` : 'Chưa có hạn nộp'
        meta.textContent = `${dueText} · ${submissionTypeLabel(assignment.submission_type)}`

        info.appendChild(title)
        info.appendChild(meta)

        if (assignment.content) {
            const content = document.createElement('p')
            content.className = 'mt-2 text-xs text-slate-300'
            content.textContent = assignment.content
            info.appendChild(content)
        }

        if (assignment.attachment_name) {
            const file = document.createElement('p')
            file.className = 'mt-2 text-xs text-emerald-300'
            file.innerHTML = '<i class="fa-solid fa-paperclip mr-1"></i>'
            file.append(document.createTextNode(assignment.attachment_name))
            info.appendChild(file)
        }

        const badge = document.createElement('span')
        const published = assignment.status === 'published'
        badge.className = `shrink-0 rounded-full border px-2 py-1 text-[11px] font-semibold ${published ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300' : 'border-slate-600 bg-slate-800/50 text-slate-400'}`
        badge.textContent = published ? 'Đã giao' : 'Nháp'

        top.appendChild(info)
        top.appendChild(badge)
        row.appendChild(top)

        const footer = document.createElement('div')
        footer.className = 'mt-3 flex items-center justify-between gap-3 text-xs text-slate-400'

        const count = document.createElement('span')
        const ungraded = Number(assignment.ungraded_submissions_count || 0)
        count.textContent = `${assignment.submissions_count || 0} bài nộp${ungraded > 0 ? ` · ${ungraded} chưa chấm` : ''}`

        const submissionsBtn = document.createElement('button')
        submissionsBtn.type = 'button'
        submissionsBtn.className = 'inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-600 text-slate-300 hover:bg-slate-700'
        submissionsBtn.setAttribute('title', 'Xem bài nộp')
        submissionsBtn.setAttribute('aria-label', `Xem bài nộp - ${assignment.title || 'Bài tập'}`)
        submissionsBtn.dataset.assignmentSubmissions = String(assignment.id)
        submissionsBtn.innerHTML = '<i class="fa-solid fa-clipboard-check"></i>'
        submissionsBtn.addEventListener('click', function () {
            loadAssignmentSubmissions(assignment)
        })

        footer.appendChild(count)
        footer.appendChild(submissionsBtn)
        row.appendChild(footer)

        return row
    }

    async function loadAssignmentSubmissions(assignment) {
        if (!assignmentSubmissionsPanel) return
        const assignmentId = assignment.id
        const url = teacherAssignmentSubmissionsUrl(assignmentId)
        if (!url) {
            setAssignmentMessage('Chưa xác định được bài tập để tải bài nộp.', 'error')
            return
        }

        setAssignmentFormVisible(false)
        showAssignmentSubmissionsPanel()
        assignmentSubmissionState.assignmentId = assignmentId
        assignmentSubmissionState.submissions = []
        assignmentSubmissionState.summary = null
        assignmentSubmissionState.loading = true
        if (assignmentSubmissionsTitleEl) assignmentSubmissionsTitleEl.textContent = assignment.title || 'Bài nộp'
        if (assignmentSubmissionsSummaryEl) assignmentSubmissionsSummaryEl.textContent = 'Đang tải bài nộp...'
        if (assignmentSubmissionsListEl) assignmentSubmissionsListEl.innerHTML = ''
        clearAssignmentSubmissionsMessage()

        try {
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
            })
            const payload = await response.json().catch(() => ({}))
            if (!response.ok) {
                throw new Error(assignmentErrorMessage(payload, 'Không tải được bài nộp.'))
            }
            applyAssignmentSubmissionsPayload(payload)
        } catch (error) {
            console.error(error)
            if (assignmentSubmissionsSummaryEl) assignmentSubmissionsSummaryEl.textContent = 'Không tải được bài nộp.'
            setAssignmentSubmissionsMessage(error.message || 'Không tải được bài nộp. Vui lòng thử lại.', 'error')
        } finally {
            assignmentSubmissionState.loading = false
        }
    }

    function applyAssignmentSubmissionsPayload(payload) {
        assignmentSubmissionState.assignmentId = payload.assignment?.id || assignmentSubmissionState.assignmentId
        assignmentSubmissionState.submissions = payload.submissions || []
        assignmentSubmissionState.summary = payload.summary || null
        assignmentSubmissionState.query = assignmentSubmissionSearchEl ? assignmentSubmissionSearchEl.value.trim().toLowerCase() : ''
        if (assignmentSubmissionsTitleEl) assignmentSubmissionsTitleEl.textContent = payload.assignment?.title || 'Bài nộp'
        updateAssignmentSubmissionsSummary()
        renderAssignmentSubmissionsList()
        clearAssignmentSubmissionsMessage()
    }

    function updateAssignmentSubmissionsSummary() {
        if (!assignmentSubmissionsSummaryEl) return
        const summary = assignmentSubmissionState.summary
        if (!summary) {
            assignmentSubmissionsSummaryEl.textContent = 'Chưa có dữ liệu bài nộp.'
            return
        }
        assignmentSubmissionsSummaryEl.textContent = `${summary.submitted}/${summary.total} đã nộp · ${summary.ungraded} chưa chấm · ${summary.graded} đã chấm`
    }

    function currentAssignmentSubmissions() {
        const query = assignmentSubmissionState.query
        const filter = assignmentSubmissionState.filter

        return assignmentSubmissionState.submissions.filter((row) => {
            const status = row.status || 'not_submitted'
            const matchesFilter = filter === 'all'
                || (filter === 'submitted' && status !== 'not_submitted')
                || (filter === 'ungraded' && row.submission_id && status !== 'returned')
                || (filter === 'late' && row.is_late)
                || status === filter
            const haystack = `${row.student_name || ''} ${row.student_email || ''}`.toLowerCase()
            const matchesQuery = !query || haystack.includes(query)

            return matchesFilter && matchesQuery
        })
    }

    function renderAssignmentSubmissionsList() {
        if (!assignmentSubmissionsListEl) return
        assignmentSubmissionsListEl.innerHTML = ''

        const rows = currentAssignmentSubmissions()
        if (rows.length === 0) {
            setAssignmentSubmissionsMessage(assignmentSubmissionState.submissions.length === 0 ? 'Lớp chưa có học viên.' : 'Không tìm thấy bài nộp phù hợp.')
            return
        }

        clearAssignmentSubmissionsMessage()
        rows.forEach((row) => {
            assignmentSubmissionsListEl.appendChild(createAssignmentSubmissionRow(row))
        })
    }

    function createAssignmentSubmissionRow(row) {
        const item = document.createElement('div')
        item.className = 'rounded-xl border border-slate-700 bg-slate-950/35 p-3'

        const header = document.createElement('div')
        header.className = 'flex items-start justify-between gap-3'

        const info = document.createElement('div')
        info.className = 'min-w-0'

        const name = document.createElement('p')
        name.className = 'truncate text-sm font-semibold text-white'
        name.textContent = row.student_name || 'Học viên'

        const email = document.createElement('p')
        email.className = 'mt-1 truncate text-xs text-slate-400'
        email.textContent = row.student_email || ''

        info.appendChild(name)
        if (row.student_email) info.appendChild(email)
        header.appendChild(info)

        const status = document.createElement('span')
        status.className = assignmentSubmissionStatusClass(row.status)
        status.textContent = assignmentSubmissionStatusLabel(row.status)
        header.appendChild(status)
        item.appendChild(header)

        const meta = document.createElement('p')
        meta.className = 'mt-2 text-xs text-slate-400'
        meta.textContent = row.submitted_at ? `Nộp lúc ${row.submitted_at}${row.is_late ? ' · Nộp trễ' : ''}` : 'Chưa có bài nộp'
        item.appendChild(meta)

        if (row.content) {
            const content = document.createElement('p')
            content.className = 'mt-2 rounded-lg border border-slate-700 bg-slate-900/60 p-2 text-xs leading-relaxed text-slate-300'
            content.textContent = row.content
            item.appendChild(content)
        }

        if (row.attachment_name) {
            const file = document.createElement('p')
            file.className = 'mt-2 text-xs text-emerald-300'
            file.innerHTML = '<i class="fa-solid fa-paperclip mr-1"></i>'
            file.append(document.createTextNode(row.attachment_name))
            item.appendChild(file)
        }

        if (row.score !== null || row.feedback || row.graded_at) {
            item.appendChild(createAssignmentGradePreview(row))
        }

        if (row.submission_id) {
            item.appendChild(createAssignmentGradeForm(row))
        }

        return item
    }

    function createAssignmentGradePreview(row) {
        const preview = document.createElement('div')
        preview.className = 'mt-3 rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-2 text-xs text-slate-200'
        const scoreText = row.score !== null && row.score !== undefined && row.score !== '' ? `Điểm ${row.score}/10` : 'Chưa nhập điểm'
        const gradedText = row.graded_at ? ` · ${row.graded_at}` : ''
        const score = document.createElement('p')
        score.className = 'font-semibold text-emerald-200'
        score.textContent = `${scoreText}${gradedText}`
        preview.appendChild(score)

        if (row.feedback) {
            const feedback = document.createElement('p')
            feedback.className = 'mt-1 leading-relaxed'
            feedback.textContent = row.feedback
            preview.appendChild(feedback)
        }

        return preview
    }

    function createAssignmentGradeForm(row) {
        const wrapper = document.createElement('div')
        wrapper.className = 'mt-3'

        const toggle = document.createElement('button')
        toggle.type = 'button'
        toggle.className = 'inline-flex h-8 items-center gap-2 rounded-lg border border-slate-600 px-3 text-xs font-semibold text-slate-200 hover:bg-slate-800'
        toggle.innerHTML = '<i class="fa-solid fa-pen-to-square"></i><span>Chấm bài</span>'
        wrapper.appendChild(toggle)

        const form = document.createElement('form')
        form.className = 'mt-3 hidden space-y-2'
        form.setAttribute('data-assignment-grade-form', '')
        form.setAttribute('data-submission-id', row.submission_id)

        const score = document.createElement('input')
        score.type = 'number'
        score.name = 'score'
        score.min = '0'
        score.max = '10'
        score.step = '0.25'
        score.placeholder = 'Điểm /10'
        score.value = row.score ?? ''
        score.className = 'w-full h-9 rounded-lg border border-slate-700 bg-slate-950/60 px-3 text-sm text-white outline-none focus:border-emerald-500'
        form.appendChild(score)

        const feedback = document.createElement('textarea')
        feedback.name = 'feedback'
        feedback.rows = 3
        feedback.placeholder = 'Nhận xét cho học viên...'
        feedback.value = row.feedback || ''
        feedback.className = 'w-full rounded-lg border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm text-white outline-none focus:border-emerald-500'
        form.appendChild(feedback)

        const submit = document.createElement('button')
        submit.type = 'submit'
        submit.className = 'inline-flex h-9 items-center justify-center rounded-lg bg-emerald-500 px-4 text-sm font-semibold text-white hover:bg-emerald-600'
        submit.textContent = 'Lưu đánh giá'
        form.appendChild(submit)

        toggle.addEventListener('click', function () {
            form.classList.toggle('hidden')
            if (!form.classList.contains('hidden')) {
                score.focus()
            }
        })

        wrapper.appendChild(form)
        return wrapper
    }

    function assignmentSubmissionStatusLabel(status) {
        if (status === 'returned') return 'Đã chấm'
        if (status === 'late') return 'Nộp trễ'
        if (status === 'submitted') return 'Đã nộp'
        return 'Chưa nộp'
    }

    function assignmentSubmissionStatusClass(status) {
        const base = 'shrink-0 rounded-full border px-2 py-1 text-[11px] font-semibold'
        if (status === 'returned') return `${base} border-emerald-500/40 bg-emerald-500/10 text-emerald-300`
        if (status === 'late') return `${base} border-amber-500/40 bg-amber-500/10 text-amber-300`
        if (status === 'submitted') return `${base} border-sky-500/40 bg-sky-500/10 text-sky-300`
        return `${base} border-slate-600 bg-slate-800/50 text-slate-400`
    }

    async function submitAssignmentGradeForm(form) {
        const submissionId = form.getAttribute('data-submission-id')
        const url = teacherAssignmentGradeUrl(submissionId)
        if (!url) {
            setAssignmentSubmissionsMessage('Chưa xác định được bài nộp để chấm.', 'error')
            return
        }

        const submit = form.querySelector('button[type="submit"]')
        if (submit) {
            submit.disabled = true
            submit.classList.add('opacity-60', 'cursor-not-allowed')
        }

        const formData = new FormData(form)
        const body = {
            score: String(formData.get('score') || ''),
            feedback: String(formData.get('feedback') || ''),
        }

        setAssignmentSubmissionsMessage('Đang lưu đánh giá...')

        try {
            const response = await fetch(url, {
                method: 'PUT',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(body),
            })
            const payload = await response.json().catch(() => ({}))
            if (!response.ok) {
                throw new Error(assignmentErrorMessage(payload, 'Không lưu được đánh giá.'))
            }
            applyAssignmentSubmissionsPayload(payload)
            showToast('success', 'Đã lưu đánh giá.')
            if (assignmentState.sessionId) {
                loadAssignmentsForSession({ session_id: assignmentState.sessionId })
            }
        } catch (error) {
            console.error(error)
            const message = error.message || 'Không lưu được đánh giá. Vui lòng thử lại.'
            setAssignmentSubmissionsMessage(message, 'error')
            showToast('error', message)
        } finally {
            if (submit) {
                submit.disabled = false
                submit.classList.remove('opacity-60', 'cursor-not-allowed')
            }
        }
    }

    function submissionTypeLabel(type) {
        if (type === 'text') return 'Nộp text'
        if (type === 'file') return 'Nộp file'
        return 'Nộp text hoặc file'
    }

    function submissionStatusLabel(status) {
        if (status === 'late') return 'Nộp trễ'
        if (status === 'returned') return 'Đã chấm'
        return 'Đã nộp'
    }

    function assignmentSubmissionUrl(assignmentId) {
        if (!assignmentSubmissionBaseUrl || !assignmentId) return ''
        return `${assignmentSubmissionBaseUrl}/${assignmentId}/submission`
    }

    function teacherAssignmentSubmissionsUrl(assignmentId) {
        if (!assignmentSubmissionsBaseUrl || !assignmentId) return ''
        return `${assignmentSubmissionsBaseUrl}/${assignmentId}/submissions`
    }

    function teacherAssignmentGradeUrl(submissionId) {
        if (!assignmentGradeBaseUrl || !submissionId) return ''
        return `${assignmentGradeBaseUrl}/${submissionId}/grade`
    }

    function createStudentAssignmentRow(assignment) {
        const row = document.createElement('div')
        row.className = 'rounded-xl border border-slate-700 bg-slate-900/45 p-3'

        const header = document.createElement('div')
        header.className = 'flex items-start justify-between gap-3'

        const info = document.createElement('div')
        info.className = 'min-w-0'

        const title = document.createElement('p')
        title.className = 'text-sm font-semibold text-white'
        title.textContent = assignment.title || 'Bài tập'

        const meta = document.createElement('p')
        meta.className = 'mt-1 text-xs text-slate-400'
        const dueText = assignment.due_at ? `Hạn ${assignment.due_at}` : 'Chưa có hạn nộp'
        meta.textContent = `${dueText} · ${submissionTypeLabel(assignment.submission_type)}`

        info.appendChild(title)
        info.appendChild(meta)
        header.appendChild(info)

        const badge = document.createElement('span')
        const hasSubmission = Boolean(assignment.submission)
        badge.className = `shrink-0 rounded-full border px-2 py-1 text-[11px] font-semibold ${hasSubmission ? 'border-emerald-500/40 bg-emerald-500/10 text-emerald-300' : 'border-amber-500/40 bg-amber-500/10 text-amber-300'}`
        badge.textContent = hasSubmission ? submissionStatusLabel(assignment.submission.status) : 'Chưa nộp'
        header.appendChild(badge)
        row.appendChild(header)

        if (assignment.content) {
            const content = document.createElement('p')
            content.className = 'mt-3 rounded-lg border border-slate-700 bg-slate-950/40 p-3 text-xs leading-relaxed text-slate-300'
            content.textContent = assignment.content
            row.appendChild(content)
        }

        if (assignment.attachment_name) {
            const file = document.createElement('p')
            file.className = 'mt-2 text-xs text-emerald-300'
            file.innerHTML = '<i class="fa-solid fa-paperclip mr-1"></i>'
            file.append(document.createTextNode(assignment.attachment_name))
            row.appendChild(file)
        }

        if (assignment.submission) {
            row.appendChild(createSubmissionPreview(assignment.submission))
        }

        row.appendChild(createStudentSubmissionForm(assignment))

        return row
    }

    function createSubmissionPreview(submission) {
        const preview = document.createElement('div')
        preview.className = 'mt-3 rounded-lg border border-emerald-500/30 bg-emerald-500/10 p-3 text-xs text-emerald-100'

        const submittedAt = document.createElement('p')
        submittedAt.className = 'font-semibold text-emerald-200'
        submittedAt.textContent = submission.submitted_at ? `Đã nộp lúc ${submission.submitted_at}` : 'Đã nộp bài'
        preview.appendChild(submittedAt)

        if (submission.status === 'returned') {
            const grade = document.createElement('p')
            grade.className = 'mt-2 font-semibold text-white'
            const scoreText = submission.score !== null && submission.score !== undefined && submission.score !== '' ? `Điểm ${submission.score}/10` : 'Đã có đánh giá'
            const gradedText = submission.graded_at ? ` · ${submission.graded_at}` : ''
            grade.textContent = `${scoreText}${gradedText}`
            preview.appendChild(grade)
        }

        if (submission.content) {
            const content = document.createElement('p')
            content.className = 'mt-2 leading-relaxed text-slate-200'
            content.textContent = submission.content
            preview.appendChild(content)
        }

        if (submission.attachment_name) {
            const file = document.createElement('p')
            file.className = 'mt-2 text-emerald-200'
            file.innerHTML = '<i class="fa-solid fa-paperclip mr-1"></i>'
            file.append(document.createTextNode(submission.attachment_name))
            preview.appendChild(file)
        }

        if (submission.feedback) {
            const feedback = document.createElement('p')
            feedback.className = 'mt-2 text-slate-200'
            feedback.textContent = `Nhận xét: ${submission.feedback}`
            preview.appendChild(feedback)
        }

        return preview
    }

    function createStudentSubmissionForm(assignment) {
        const form = document.createElement('form')
        form.className = 'mt-3 space-y-3'
        form.setAttribute('data-student-assignment-form', '')
        form.setAttribute('data-assignment-id', assignment.id)

        if (assignment.submission_type !== 'file') {
            const textarea = document.createElement('textarea')
            textarea.name = 'content'
            textarea.rows = 3
            textarea.className = 'w-full rounded-lg border border-slate-700 bg-slate-950/50 px-3 py-2 text-sm text-white outline-none focus:border-emerald-500'
            textarea.placeholder = 'Nhập nội dung bài nộp...'
            textarea.value = assignment.submission?.content || ''
            form.appendChild(textarea)
        }

        if (assignment.submission_type !== 'text') {
            const fileInput = document.createElement('input')
            fileInput.type = 'file'
            fileInput.name = 'attachment'
            fileInput.className = 'block w-full rounded-lg border border-slate-700 bg-slate-950/50 px-3 py-2 text-sm text-slate-300 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-700 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-600'
            form.appendChild(fileInput)
        }

        const actions = document.createElement('div')
        actions.className = 'flex items-center justify-between gap-3'

        const hint = document.createElement('p')
        hint.className = 'text-xs text-slate-400'
        hint.textContent = assignment.submission ? 'Bạn có thể cập nhật bài nộp.' : 'Nộp bài cho buổi học này.'

        const submitBtn = document.createElement('button')
        submitBtn.type = 'submit'
        submitBtn.className = 'inline-flex h-9 items-center justify-center rounded-lg bg-emerald-500 px-4 text-sm font-semibold text-white hover:bg-emerald-600'
        submitBtn.textContent = assignment.submission ? 'Cập nhật' : 'Nộp bài'

        actions.appendChild(hint)
        actions.appendChild(submitBtn)
        form.appendChild(actions)

        return form
    }

    async function submitStudentAssignmentForm(form) {
        const assignmentId = form.getAttribute('data-assignment-id')
        const url = assignmentSubmissionUrl(assignmentId)
        if (!url) {
            setAssignmentMessage('Chưa xác định được bài tập để nộp.', 'error')
            return
        }

        const submitBtn = form.querySelector('button[type="submit"]')
        if (submitBtn) {
            submitBtn.disabled = true
            submitBtn.classList.add('opacity-60', 'cursor-not-allowed')
        }

        setAssignmentMessage('Đang nộp bài...')

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: new FormData(form),
            })

            if (!response.ok) throw new Error('Không nộp được bài.')
            const payload = await response.json()
            applyAssignmentPayload(payload)
            setAssignmentMessage('Đã lưu bài nộp.')
        } catch (error) {
            console.error(error)
            setAssignmentMessage('Không nộp được bài. Vui lòng kiểm tra nội dung và thử lại.', 'error')
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false
                submitBtn.classList.remove('opacity-60', 'cursor-not-allowed')
            }
        }
    }

    async function submitAssignmentForm() {
        if (!assignmentForm) return
        if (!assignmentState.sessionId) {
            setAssignmentMessage('Chưa xác định được buổi học để giao bài. Vui lòng chọn lại buổi học.', 'error')
            return
        }

        const formData = new FormData(assignmentForm)
        const dueAt = String(formData.get('due_at') || '')
        if (dueAt === '') {
            formData.delete('due_at')
        } else if (dueAt.includes('T')) {
            formData.set('due_at', dueAt.replace('T', ' '))
        }

        setAssignmentMessage('Đang tạo bài tập...')
        if (assignmentSubmitBtn) {
            assignmentSubmitBtn.disabled = true
            assignmentSubmitBtn.classList.add('opacity-60', 'cursor-not-allowed')
        }

        try {
            const response = await fetch(assignmentUrl(assignmentState.sessionId), {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            })
            const payload = await response.json().catch(() => ({}))
            if (!response.ok) {
                throw new Error(assignmentErrorMessage(payload, 'Không tạo được bài tập.'))
            }
            assignmentForm.reset()
            applyAssignmentPayload(payload)
            setAssignmentFormVisible(false)
            showToast('success', payload.message || 'Đã tạo bài tập thành công.')
        } catch (error) {
            console.error(error)
            const message = error.message || 'Không tạo được bài tập. Vui lòng kiểm tra dữ liệu và thử lại.'
            setAssignmentMessage(message, 'error')
            showToast('error', message)
        } finally {
            if (assignmentSubmitBtn) {
                assignmentSubmitBtn.disabled = false
                assignmentSubmitBtn.classList.remove('opacity-60', 'cursor-not-allowed')
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

    document.querySelectorAll('[data-session-tab]').forEach((btn) => {
        btn.addEventListener('click', function () {
            activateSessionPanel(btn.getAttribute('data-session-tab') || 'detail')
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

    if (attendanceSearchEl) {
        attendanceSearchEl.addEventListener('input', function () {
            attendanceState.query = attendanceSearchEl.value.trim().toLowerCase()
            renderAttendanceList()
        })
    }

    document.querySelectorAll('[data-attendance-filter]').forEach((btn) => {
        btn.addEventListener('click', function () {
            attendanceState.filter = btn.getAttribute('data-attendance-filter') || 'all'
            document.querySelectorAll('[data-attendance-filter]').forEach((filterBtn) => {
                const active = filterBtn.getAttribute('data-attendance-filter') === attendanceState.filter
                filterBtn.classList.toggle('is-active', active)
            })
            renderAttendanceList()
        })
    })

    document.querySelectorAll('[data-bulk-attendance]').forEach((btn) => {
        btn.addEventListener('click', function () {
            const status = btn.getAttribute('data-bulk-attendance')
            if (status) bulkUpdateAttendance(status)
        })
    })

    if (attendanceRefreshBtn) {
        attendanceRefreshBtn.addEventListener('click', function () {
            if (attendanceState.sessionId) {
                loadAttendanceForSession({ session_id: attendanceState.sessionId })
            }
        })
    }

    if (assignmentRefreshBtn) {
        assignmentRefreshBtn.addEventListener('click', function () {
            if (assignmentState.sessionId) {
                loadAssignmentsForSession({ session_id: assignmentState.sessionId })
            }
        })
    }

    if (assignmentSubmissionsCloseBtn) {
        assignmentSubmissionsCloseBtn.addEventListener('click', function () {
            resetAssignmentSubmissionsPanel()
        })
    }

    if (assignmentSubmissionsPanel) {
        assignmentSubmissionsPanel.addEventListener('click', function (event) {
            if (event.target === assignmentSubmissionsPanel) {
                resetAssignmentSubmissionsPanel()
            }
        })
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && assignmentSubmissionsPanel && !assignmentSubmissionsPanel.classList.contains('hidden')) {
            resetAssignmentSubmissionsPanel()
        }
    })

    if (assignmentSubmissionSearchEl) {
        assignmentSubmissionSearchEl.addEventListener('input', function () {
            assignmentSubmissionState.query = assignmentSubmissionSearchEl.value.trim().toLowerCase()
            renderAssignmentSubmissionsList()
        })
    }

    document.querySelectorAll('[data-assignment-submission-filter]').forEach((btn) => {
        btn.addEventListener('click', function () {
            assignmentSubmissionState.filter = btn.getAttribute('data-assignment-submission-filter') || 'all'
            document.querySelectorAll('[data-assignment-submission-filter]').forEach((filterBtn) => {
                const active = filterBtn.getAttribute('data-assignment-submission-filter') === assignmentSubmissionState.filter
                filterBtn.classList.toggle('is-active', active)
            })
            renderAssignmentSubmissionsList()
        })
    })

    if (assignmentFormToggle) {
        assignmentFormToggle.addEventListener('click', function () {
            const isHidden = assignmentFormShell ? assignmentFormShell.classList.contains('hidden') : true
            setAssignmentFormVisible(isHidden)
        })
    }

    if (assignmentForm && assignmentMode !== 'teacher') {
        assignmentForm.addEventListener('submit', function (event) {
            event.preventDefault()
            submitAssignmentForm()
        })
    }

    if (assignmentSubmitBtn && assignmentMode !== 'teacher') {
        assignmentSubmitBtn.addEventListener('click', function (event) {
            event.preventDefault()
            submitAssignmentForm()
        })
    }

    document.addEventListener(
        'submit',
        function (event) {
            if (assignmentMode !== 'teacher' && event.target && event.target.matches('[data-assignment-form]')) {
                event.preventDefault()
                submitAssignmentForm()
                return
            }

            if (event.target && event.target.matches('[data-student-assignment-form]')) {
                event.preventDefault()
                submitStudentAssignmentForm(event.target)
                return
            }

            if (event.target && event.target.matches('[data-assignment-grade-form]')) {
                event.preventDefault()
                submitAssignmentGradeForm(event.target)
            }
        },
        true
    )

    if (initialEvents.length > 0) {
        const initialState = parseQueryState()
        syncFormState(initialState)
        const selectedInitial = initialEvents.find((eventItem) => String(eventItem.id) === String(initialState.session_id))
        updateDetail((selectedInitial || initialEvents[0]).extendedProps || null)
    } else {
        updateDetail(null)
    }
    activateSessionPanel(activeSessionPanel)

    window.addEventListener('popstate', function () {
        fetchAndRender(parseQueryState(), true)
    })
})
