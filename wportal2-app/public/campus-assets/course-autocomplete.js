(() => {
    const search = document.getElementById('course-lookup');
    const select = document.getElementById('course-options');
    if (!search || !select) return;
    const options = Array.from(select.options).slice(1).map(option => option.cloneNode(true));
    const status = document.getElementById('course-search-status');
    const year = document.getElementById('course-academic-year');
    const professor = document.getElementById('selected-professor');
    const normalize = value => value.normalize('NFKC').toLocaleLowerCase().trim();
    const updateSelection = () => {
        const selected = select.selectedOptions[0];
        professor.textContent = selected?.value ? `担当教員：${selected.dataset.professor || '未登録'}` : '';
        year.readOnly = false;
    };
    search.addEventListener('input', () => {
        if (search.composing) return;
        const previous = select.value;
        const terms = normalize(search.value).split(/\s+/).filter(Boolean);
        const matches = options.filter(option => terms.every(term => normalize(option.textContent).includes(term)));
        select.replaceChildren(new Option('授業を選択してください', ''));
        matches.forEach(option => select.add(option.cloneNode(true)));
        select.value = matches.some(option => option.value === previous) ? previous : '';
        status.textContent = matches.length ? `${matches.length}件の候補から選択してください。` : '該当する授業がありません。検索語を変えてください。';
        updateSelection();
    });
    search.addEventListener('compositionstart', () => { search.composing = true; });
    search.addEventListener('compositionend', () => { search.composing = false; search.dispatchEvent(new Event('input')); });
    select.addEventListener('change', updateSelection);
    updateSelection();
})();
