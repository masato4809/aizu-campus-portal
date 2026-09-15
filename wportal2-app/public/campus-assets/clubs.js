(() => {
    const input = document.querySelector('[data-club-images]');
    if (!input) return;
    const form = document.querySelector('[data-club-form]');
    const preview = document.querySelector('[data-club-preview]');
    const error = document.querySelector('#club-image-error');
    const clear = document.querySelector('[data-club-clear]');
    let urls = [];

    function update() {
        urls.forEach((url) => URL.revokeObjectURL(url));
        urls = [];
        preview.replaceChildren();
        const files = Array.from(input.files);
        const message = files.length > 4
            ? '画像は最大4枚まで選んでください。'
            : files.some((file) => file.size > 5 * 1024 * 1024)
                ? '画像は1枚5MB以下にしてください。'
                : files.some((file) => !['image/jpeg', 'image/png', 'image/webp'].includes(file.type))
                    ? 'JPEG・PNG・WebPの画像を選んでください。'
                    : '';
        error.textContent = message;
        error.hidden = !message;
        input.setCustomValidity(message);
        clear.hidden = files.length === 0;
        if (message) return;
        files.forEach((file) => {
            const figure = document.createElement('figure');
            const img = document.createElement('img');
            const caption = document.createElement('figcaption');
            const url = URL.createObjectURL(file);
            urls.push(url);
            img.src = url;
            img.alt = file.name;
            caption.textContent = file.name;
            figure.append(img, caption);
            preview.append(figure);
        });
    }
    input.addEventListener('change', update);
    clear.addEventListener('click', () => { input.value = ''; update(); });
    form.addEventListener('submit', () => {
        const button = form.querySelector('[type="submit"]');
        button.disabled = true;
        button.textContent = '投稿しています…';
    });
    window.addEventListener('pageshow', () => {
        const button = form.querySelector('[type="submit"]');
        button.disabled = false;
        button.textContent = '投稿する';
        update();
    });
})();
