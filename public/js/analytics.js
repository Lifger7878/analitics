(() => {
    const data = window.analyticsData || {};
    const shapes = window.analyticsMap || [];
    const map = document.getElementById('license-map');
    const panel = document.getElementById('detail-panel');
    const readout = document.getElementById('hover-readout');
    const empty = document.getElementById('map-empty');
    let selected = null;

    const formatNumber = (value) => new Intl.NumberFormat('uk-UA').format(value);
    const heatFill = (id) => {
        if (id === selected) return '#1e40af';
        const region = data[id];
        if (!region) return '#e2e8f0';
        const t = Math.min(region.issued / 150, 1);
        return `rgb(${Math.round(219 - 189 * t)},${Math.round(234 - 176 * t)},${Math.round(254 - 116 * t)})`;
    };

    const svg = (tag, attrs = {}) => {
        const element = document.createElementNS('http://www.w3.org/2000/svg', tag);
        Object.entries(attrs).forEach(([key, value]) => element.setAttribute(key, value));
        return element;
    };

    function renderMap() {
        if (!shapes.length) {
            empty.hidden = false;
            return;
        }
        const defs = svg('defs');
        const glow = svg('filter', { id: 'glow' });
        glow.appendChild(svg('feDropShadow', { dx: '0', dy: '0', stdDeviation: '4', floodColor: '#3b82f6', floodOpacity: '.5' }));
        const shadow = svg('filter', { id: 'shadow' });
        shadow.appendChild(svg('feDropShadow', { dx: '0', dy: '1', stdDeviation: '2', floodColor: '#1e3a8a', floodOpacity: '.2' }));
        defs.append(glow, shadow);
        map.appendChild(defs);

        shapes.forEach((shape) => {
            const region = data[shape.id];
            const group = svg('g', { class: 'oblast', 'data-region': shape.id });
            const path = svg('path', { d: shape.path, fill: heatFill(shape.id), stroke: 'white', 'stroke-width': '.8' });
            if (shape.transform) path.setAttribute('transform', shape.transform);
            const label = svg('text', {
                x: shape.cx,
                y: Number(shape.cy) + 4,
                'text-anchor': 'middle',
                'font-size': shape.fontSize || (shape.id === 'kyiv_city' ? 7 : ['zakarpat', 'chernivtsi'].includes(shape.id) ? 8 : 10),
                'font-family': 'Manrope, sans-serif',
                'font-weight': '600',
                fill: '#1e3a5f',
                'paint-order': 'stroke',
                stroke: 'white',
                'stroke-width': '2',
                class: 'map-label',
            });
            label.textContent = region ? region.name : shape.id;
            group.append(path, label);
            group.addEventListener('mouseenter', () => showHover(shape.id));
            group.addEventListener('mouseleave', () => { readout.hidden = true; });
            group.addEventListener('click', () => selectRegion(selected === shape.id ? null : shape.id));
            map.appendChild(group);
        });
    }

    function showHover(id) {
        const region = data[id];
        if (!region) return;
        readout.hidden = false;
        readout.innerHTML = `<strong>${region.name}</strong> &mdash; <b>${region.issued}</b> видано, <em>${region.revoked}</em> анульовано`;
    }

    function selectRegion(id) {
        selected = id;
        document.querySelectorAll('.oblast').forEach((group) => {
            const isSelected = group.dataset.region === id;
            group.style.opacity = id && !isSelected ? '.62' : '1';
            const path = group.querySelector('path');
            const label = group.querySelector('text');
            path.setAttribute('fill', heatFill(group.dataset.region));
            path.setAttribute('stroke-width', isSelected ? '2' : '.8');
            path.style.filter = isSelected ? 'url(#glow)' : '';
            label.setAttribute('fill', isSelected ? 'white' : '#1e3a5f');
            label.setAttribute('stroke-width', isSelected ? '0' : '2');
            group.querySelector('.badge')?.remove();
            if (regionFor(group.dataset.region) && isSelected) addBadge(group, group.dataset.region, true);
        });
        if (id) {
            panel.classList.add('open');
            panel.setAttribute('aria-hidden', 'false');
            fillPanel(id);
        } else {
            panel.classList.remove('open');
            panel.setAttribute('aria-hidden', 'true');
        }
    }

    function regionFor(id) { return data[id]; }

    function addBadge(group, id, active) {
        if (id === 'kyiv_city') return;
        const shape = shapes.find((item) => item.id === id);
        const region = data[id];
        const badge = svg('g', { class: 'badge' });
        badge.appendChild(svg('circle', { cx: shape.cx, cy: Number(shape.cy) + 17, r: 11, fill: active ? '#1e40af' : '#3b82f6' }));
        const number = svg('text', { x: shape.cx, y: Number(shape.cy) + 21, 'text-anchor': 'middle', 'font-size': '8.5', 'font-family': 'Manrope, sans-serif', 'font-weight': '800', fill: 'white' });
        number.textContent = region.issued;
        badge.appendChild(number);
        group.appendChild(badge);
    }

    function fillPanel(id) {
        const region = data[id];
        const trend = region.weeklyIssued.at(-1) - region.weeklyIssued.at(-2);
        const ratio = Math.round((region.issued / (region.issued + region.revoked)) * 100);
        document.getElementById('detail-name').textContent = region.name;
        const trendElement = document.getElementById('detail-trend');
        trendElement.classList.toggle('down', trend < 0);
        trendElement.textContent = `${trend >= 0 ? '↑ +' : '↓ '}${trend} видано vs минулий тиждень`;
        document.getElementById('detail-metrics').innerHTML = [
            ['Видано за тиждень', region.issued, 'ліцензій'],
            ['Анульовано', region.revoked, 'ліцензій'],
            ['Призупинено', region.suspended, 'ліцензій'],
            ['Активних загалом', formatNumber(region.total), 'ліцензій'],
        ].map(([label, value, sub]) => `<div class="metric"><label>${label}</label><strong>${value}</strong><small>${sub}</small></div>`).join('');
        renderBars('issued-bars', region.weeklyIssued, Math.max(...region.weeklyIssued));
        renderBars('revoked-bars', region.weeklyRevoked, Math.max(...region.weeklyRevoked));
        document.getElementById('issued-ratio').style.width = `${ratio}%`;
        document.getElementById('revoked-ratio').style.width = `${100 - ratio}%`;
        document.getElementById('issued-percent').textContent = `${ratio}%`;
        document.getElementById('revoked-percent').textContent = `${100 - ratio}%`;
        permitState.region = id;
        setPopupMode('licensing');
        updatePermitAnalytics();
    }

    function renderBars(id, values, max) {
        document.getElementById(id).innerHTML = values.map((value) => `<span class="bar" style="height:${Math.max(2, (value / max) * 58)}px" title="${value}"></span>`).join('');
    }

    document.getElementById('close-panel').addEventListener('click', () => selectRegion(null));
    document.addEventListener('keydown', (event) => { if (event.key === 'Escape') selectRegion(null); });
    renderMap();

    const permitData = window.permitAnalytics || {};
    const permitState = { period: 'current', region: 'all', type: 'all', direction: 'all', purpose: 'all' };
    const periodFactors = { current: 1, quarter: .62, month: .23 };
    const periodLabels = { current: 'Поточний рік', quarter: 'Поточний квартал', month: 'Поточний місяць' };
    const formatPermitNumber = (value) => formatNumber(Math.round(value));

    function setPopupMode(mode) {
        document.querySelectorAll('.popup-mode-button').forEach((button) => {
            const active = button.dataset.popupMode === mode;
            button.classList.toggle('active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        document.querySelectorAll('.popup-view').forEach((view) => {
            view.classList.toggle('is-hidden', view.id !== `popup-${mode === 'licensing' ? 'license' : mode}-view`);
        });
    }

    function selectedRegionFactor() {
        if (permitState.region === 'all') return 1;
        const region = data[permitState.region];
        const total = Object.values(data).reduce((sum, item) => sum + item.total, 0);
        return region && total ? Math.max(.08, region.total / total * 8) : 1;
    }

    function typeRows() {
        const selected = permitState.type;
        return (permitData.types || []).map((item) => ({
            ...item,
            value: selected === 'all' || selected === item.id ? item.value : 0,
        }));
    }

    function filteredIrregular() {
        const source = permitData.irregular || [];
        const directionMap = { international: 'Міжнародні', internal: 'Внутрішні' };
        return source.map((item) => ({
            ...item,
            value: permitState.direction === 'all' || directionMap[permitState.direction] === item.label ? item.value : 0,
        }));
    }

    function filteredPurposes() {
        const source = permitData.purposes || [];
        return source.map((item) => ({
            ...item,
            value: item.value,
        }));
    }

    function renderPermitTypes(rows) {
        const max = Math.max(...rows.map((item) => item.value), 1);
        document.getElementById('popup-permit-types').innerHTML = rows.map((item) => `<div class="permit-type-row"><span class="type-dot ${item.color}"></span><span>${item.label}</span><strong>${formatPermitNumber(item.value)}</strong><i><b class="${item.color}" style="width:${(item.value / max) * 100}%"></b></i></div>`).join('');
    }

    function renderPermitBars(rows) {
        const max = Math.max(...rows.map((item) => item.value), 1);
        document.getElementById('popup-irregular').innerHTML = rows.map((item) => `<div class="vertical-bar-item"><div class="vertical-bar"><b style="height:${(item.value / max) * 100}%"></b></div><strong>${item.value}</strong><span>${item.label}</span></div>`).join('');
    }

    function renderPermitPurposes(rows) {
        const max = Math.max(...rows.map((item) => item.value), 1);
        document.getElementById('popup-purposes').innerHTML = rows.map((item) => `<div class="purpose-row"><span>${item.label}</span><strong>${item.value}</strong><i><b style="width:${(item.value / max) * 100}%"></b></i></div>`).join('');
    }

    function updatePermitAnalytics() {
        const factor = periodFactors[permitState.period] * selectedRegionFactor();
        const typeRowsData = typeRows();
        const typeTotal = typeRowsData.reduce((sum, item) => sum + item.value, 0);
        const directionRows = filteredIrregular();
        const purposeRows = filteredPurposes();
        const typeFactor = permitState.type === 'all' ? 1 : Math.max(.12, typeTotal / permitData.types.reduce((sum, item) => sum + item.value, 0));
        const directionFactor = permitState.direction === 'all' ? 1 : .48;
        const purposeFactor = permitState.purpose === 'all' ? 1 : .42;

        document.getElementById('popup-carriers').textContent = formatPermitNumber(permitData.carriers * factor * typeFactor);
        document.getElementById('popup-border-points').textContent = formatPermitNumber(permitData.borderPoints * selectedRegionFactor() * (permitState.direction === 'all' ? 1 : .72));
        document.getElementById('popup-ekmt-applications').textContent = formatPermitNumber(permitData.ekmtApplications * factor * typeFactor * directionFactor * purposeFactor);
        renderPermitTypes(typeRowsData.map((item) => ({ ...item, value: Math.round(item.value * factor) })));
        renderPermitBars(directionRows.map((item) => ({ ...item, value: Math.round(item.value * factor) })));
        renderPermitPurposes(purposeRows.map((item) => ({ ...item, value: Math.round(item.value * factor) })));
    }

    function setMode(mode) {
        document.querySelectorAll('.mode-tab').forEach((tab) => {
            const active = tab.dataset.mode === mode;
            tab.classList.toggle('active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        document.querySelectorAll('[data-mode-content]').forEach((element) => element.classList.toggle('is-hidden', element.dataset.modeContent !== mode));
        document.getElementById('license-dashboard').classList.toggle('is-hidden', mode !== 'licensing');
        document.getElementById('permits-dashboard').classList.toggle('is-hidden', mode !== 'permits');
        if (mode === 'permits') selectRegion(null);
    }

    document.getElementById('popup-permit-type').addEventListener('change', (event) => {
        permitState.type = event.target.value;
        updatePermitAnalytics();
    });
    document.querySelectorAll('.popup-mode-button').forEach((button) => {
        button.addEventListener('click', () => setPopupMode(button.dataset.popupMode));
    });
    updatePermitAnalytics();
})();
