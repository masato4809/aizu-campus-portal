(() => {
    const form = document.getElementById('course-review-form');
    if (!form) return;
    const steps = Array.from(form.querySelectorAll('.review-step'));
    if (steps.length < 2) return;
    const indicator = document.querySelector('.review-step-indicator');
    const stepTitles = steps.map(step => step.querySelector('h2')?.textContent.replace(/^STEP\s*\d+：/, '') || '');
    let current = 0;

    const summaryValue = field => {
        const checked = Array.from(field.querySelectorAll('input:checked'));
        if (checked.length) return checked.map(input => input.closest('label')?.textContent.trim() || input.value).join('、');
        const select = field.querySelector('select');
        if (select) return select.options[select.selectedIndex]?.textContent.trim() || '';
        const inputs = Array.from(field.querySelectorAll('input:not([type=radio]):not([type=checkbox]), textarea')).filter(input => input.value.trim());
        if (inputs.length > 1) {
            return inputs.map(input => {
                const label = input.closest('label')?.childNodes[0]?.textContent.trim();
                return label ? `${label}：${input.value.trim()}` : input.value.trim();
            }).join('、');
        }
        return inputs[0]?.value.trim() || '';
    };

    const buildSummary = () => {
        const summary = document.getElementById('review-summary');
        if (!summary) return;
        summary.replaceChildren();
        form.querySelectorAll('[data-summary]').forEach(field => {
            const value = summaryValue(field);
            const row = document.createElement('p');
            const label = document.createElement('strong');
            label.textContent = `${field.dataset.summary}：`;
            row.append(label, document.createTextNode(value || '(未入力)'));
            summary.appendChild(row);
        });
    };

    const showStep = (index, scroll = true) => {
        steps.forEach((step, i) => { step.hidden = i !== index; });
        if (indicator) indicator.textContent = `ステップ ${index + 1} / ${steps.length}：${stepTitles[index]}（${index === steps.length - 1 ? '確認して投稿すると完了' : 'あと' + (steps.length - index - 1) + 'ステップ'}）`;
        const progress = document.getElementById('review-step-progress');
        if (progress) progress.value = index + 1;
        document.querySelectorAll('.review-progress-labels > span').forEach((item, i) => {
            if (i === index) item.setAttribute('aria-current', 'step');
            else item.removeAttribute('aria-current');
        });
        if (index === steps.length - 1) buildSummary();
        if (scroll) document.getElementById('review-progress')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const validateStep = index => {
        const controls = steps[index].querySelectorAll('input, select, textarea');
        for (const control of controls) {
            if (!control.reportValidity()) return false;
        }
        return true;
    };

    form.querySelectorAll('[data-step-next]').forEach(button => {
        button.addEventListener('click', () => {
            if (!validateStep(current)) return;
            current = Math.min(current + 1, steps.length - 1);
            showStep(current);
        });
    });
    form.querySelectorAll('[data-step-back]').forEach(button => {
        button.addEventListener('click', () => {
            current = Math.max(current - 1, 0);
            showStep(current);
        });
    });

    showStep(0, false);
})();
