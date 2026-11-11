(function () {
    var miliHead = '<img src="/IMG/mascotas3d/mili_front.png" alt="" style="width:32px;height:32px;object-fit:contain;display:block;">';
    var luluHead = '<img src="/IMG/mascotas3d/lulu_front.png" alt="" style="width:32px;height:32px;object-fit:contain;display:block;">';

    function applyTheme(theme, button) {
        document.documentElement.setAttribute('data-theme', theme);
        button.setAttribute('data-theme', theme);
        button.setAttribute('aria-pressed', String(theme === 'dark'));
        button.setAttribute('aria-label', theme === 'dark' ? 'Cambiar a tema claro' : 'Cambiar a tema oscuro');
        button.title = theme === 'dark' ? 'Tema oscuro: cambiar a claro' : 'Tema claro: cambiar a oscuro';
        button.querySelector('.handapp-theme-toggle__thumb').innerHTML = theme === 'dark' ? luluHead : miliHead;
        button.querySelector('.handapp-theme-toggle__label').textContent = theme === 'dark' ? 'Oscuro' : 'Claro';
        try {
            localStorage.setItem('handapp-theme', theme);
        } catch (error) {
            console.warn('No se pudo guardar la preferencia de tema.', error);
        }
    }

    function createToggle() {
        if (document.querySelector('.handapp-theme-toggle')) {
            return;
        }

        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'handapp-theme-toggle';
        button.innerHTML = '<span class="handapp-theme-toggle__track"><span class="handapp-theme-toggle__icon">' + miliHead + '</span><span class="handapp-theme-toggle__icon">' + luluHead + '</span><span class="handapp-theme-toggle__thumb" aria-hidden="true"></span></span><span class="handapp-theme-toggle__label"></span>';
        button.addEventListener('click', function () {
            var nextTheme = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
            applyTheme(nextTheme, button);
        });

        document.body.appendChild(button);
        applyTheme(document.documentElement.getAttribute('data-theme') === 'dark' ? 'dark' : 'light', button);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', createToggle);
    } else {
        createToggle();
    }
}());
