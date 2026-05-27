function createMessage(role, content) {
    return { role, content };
}

function appendMessage(container, role, content) {
    const bubble = document.createElement('div');
    const isUser = role === 'user';
    bubble.className = [
        'max-w-[85%] rounded-2xl px-3 py-2 text-sm shadow-sm whitespace-pre-wrap',
        isUser
            ? 'ml-auto rounded-tr-sm bg-[#f05123] text-white'
            : 'rounded-tl-sm bg-white text-gray-700',
    ].join(' ');
    bubble.textContent = content;
    container.appendChild(bubble);
    container.scrollTop = container.scrollHeight;
}

function renderSources(container, sources) {
    if (!container) return;

    container.innerHTML = '';
    if (!Array.isArray(sources) || sources.length === 0) {
        container.classList.add('hidden');
        return;
    }

    const title = document.createElement('p');
    title.className = 'mb-1 text-[11px] font-bold uppercase tracking-wide text-gray-500';
    title.textContent = 'Nguồn tham khảo';
    container.appendChild(title);

    const list = document.createElement('div');
    list.className = 'flex flex-wrap gap-1.5';

    sources.slice(0, 4).forEach((source) => {
        const link = document.createElement('a');
        link.className = 'rounded-full bg-gray-100 px-2 py-1 text-[11px] font-semibold text-gray-700 hover:bg-gray-200';
        link.href = source.url || '#';
        link.textContent = source.title || source.type || 'Nguồn';
        list.appendChild(link);
    });

    container.appendChild(list);
    container.classList.remove('hidden');
}

function initChatbot(root) {
    const toggle = root.querySelector('[data-chatbot-toggle]');
    const close = root.querySelector('[data-chatbot-close]');
    const panel = root.querySelector('[data-chatbot-panel]');
    const form = root.querySelector('[data-chatbot-form]');
    const input = root.querySelector('[data-chatbot-input]');
    const submit = root.querySelector('[data-chatbot-submit]');
    const messagesEl = root.querySelector('[data-chatbot-messages]');
    const sourcesEl = root.querySelector('[data-chatbot-sources]');

    if (!toggle || !panel || !form || !input || !messagesEl || !window.axios) return;

    const endpoint = root.getAttribute('data-chat-url');
    const pageType = root.getAttribute('data-page-type') || 'generic';
    const pageRef = root.getAttribute('data-page-ref') || '';
    const history = [];

    function openPanel() {
        panel.classList.remove('hidden');
        input.focus();
    }

    function closePanel() {
        panel.classList.add('hidden');
    }

    function setLoading(loading) {
        if (submit) {
            submit.disabled = loading;
            submit.classList.toggle('opacity-60', loading);
        }
        input.disabled = loading;
    }

    toggle.addEventListener('click', function () {
        if (panel.classList.contains('hidden')) {
            openPanel();
        } else {
            closePanel();
        }
    });

    if (close) close.addEventListener('click', closePanel);

    input.addEventListener('input', function () {
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 112) + 'px';
    });

    input.addEventListener('keydown', function (event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            form.dispatchEvent(new Event('submit', { cancelable: true }));
        }
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        const message = input.value.trim();
        if (!message) return;

        appendMessage(messagesEl, 'user', message);
        const outgoingHistory = history.slice(-6);
        history.push(createMessage('user', message));
        input.value = '';
        input.style.height = 'auto';
        setLoading(true);
        renderSources(sourcesEl, []);

        window.axios.post(endpoint, {
            message,
            page_type: pageType,
            page_ref: pageRef || null,
            history: outgoingHistory,
        }).then(function (response) {
            const data = response.data || {};
            const answer = data.message || 'Mình chưa có câu trả lời phù hợp.';
            appendMessage(messagesEl, 'assistant', answer);
            history.push(createMessage('assistant', answer));
            renderSources(sourcesEl, data.sources || []);
        }).catch(function (error) {
            const fallback = error.response && error.response.data && error.response.data.message
                ? error.response.data.message
                : 'Chatbot tạm thời chưa phản hồi được. Vui lòng thử lại sau.';
            appendMessage(messagesEl, 'assistant', fallback);
            history.push(createMessage('assistant', fallback));
        }).finally(function () {
            setLoading(false);
            input.focus();
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-global-chatbot]').forEach(initChatbot);
});
