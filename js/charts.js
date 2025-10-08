/**
 * ARCHIVO: charts.js
 * Funciones manuales para crear gráficos sin librerías externas
 */

// Función principal que se ejecuta cuando el DOM está listo
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar menú móvil
    initMobileMenu();

    // Crear gráficos
    createBarChart('barChart', getBarChartData());
    createLineChart('lineChartSvg', getLineChartData());
    createPieChart('pieChart', getPieChartData());

    // Inicializar animaciones
    animateOnScroll();
});

/**
 * Inicializa el menú móvil
 */
function initMobileMenu() {
    const menuBtn = document.getElementById('menuBtn');
    const sideNav = document.getElementById('sideNav');

    if (menuBtn && sideNav) {
        menuBtn.addEventListener('click', function() {
            sideNav.classList.toggle('hidden');

            // Animar el ícono del menú
            const icon = menuBtn.querySelector('.icon');
            if (icon) {
                icon.style.transform = sideNav.classList.contains('hidden') ? 'rotate(0)' : 'rotate(90deg)';
                icon.style.transition = 'transform 0.3s ease';
            }
        });
    }
}

/**
 * Datos para el gráfico de barras
 */
function getBarChartData() {
    return [
        { label: 'Ene', value: 65, color: '#67e8f9' },
        { label: 'Feb', value: 80, color: '#22d3ee' },
        { label: 'Mar', value: 45, color: '#06b6d4' },
        { label: 'Abr', value: 90, color: '#0891b2' },
        { label: 'May', value: 70, color: '#0e7490' },
        { label: 'Jun', value: 85, color: '#155e75' }
    ];
}

/**
 * Datos para el gráfico de líneas
 */
function getLineChartData() {
    return [
        { x: 0, y: 30, label: 'Lun' },
        { x: 1, y: 50, label: 'Mar' },
        { x: 2, y: 45, label: 'Mie' },
        { x: 3, y: 65, label: 'Jue' },
        { x: 4, y: 55, label: 'Vie' },
        { x: 5, y: 70, label: 'Sab' },
        { x: 6, y: 80, label: 'Dom' }
    ];
}

/**
 * Datos para el gráfico circular
 */
function getPieChartData() {
    return [
        { label: 'Ventas', value: 35, color: '#67e8f9' },
        { label: 'Marketing', value: 25, color: '#22d3ee' },
        { label: 'Desarrollo', value: 20, color: '#06b6d4' },
        { label: 'Soporte', value: 20, color: '#0891b2' }
    ];
}

/**
 * Crear gráfico de barras mejorado
 */
function createBarChart(containerId, data) {
    const container = document.getElementById(containerId);
    if (!container) return;

    // Limpiar contenedor
    container.innerHTML = '';

    const maxValue = Math.max(...data.map(d => d.value));

    data.forEach((item, index) => {
        const barWrapper = document.createElement('div');
        barWrapper.style.cssText = `
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
            height: 100%;
            flex: 1;
        `;

        const bar = document.createElement('div');
        bar.className = 'bar';
        bar.style.cssText = `
            width: 40px;
            height: 0%;
            background: linear-gradient(to top, ${item.color}, ${adjustColor(item.color, 20)});
            border-radius: 4px 4px 0 0;
            position: relative;
            transition: all 0.5s ease;
            cursor: pointer;
        `;

        // Animación de entrada
        setTimeout(() => {
            bar.style.height = (item.value / maxValue * 100) + '%';
        }, index * 100);

        // Tooltip
        const tooltip = document.createElement('div');
        tooltip.style.cssText = `
            position: absolute;
            top: -40px;
            background: #1f2937;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            opacity: 0;
            transition: opacity 0.3s;
            pointer-events: none;
            white-space: nowrap;
            z-index: 10;
        `;
        tooltip.textContent = `${item.label}: ${item.value}`;

        const value = document.createElement('span');
        value.className = 'bar-value';
        value.textContent = item.value;
        value.style.cssText = `
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 12px;
            font-weight: bold;
            color: #374151;
        `;

        const label = document.createElement('span');
        label.className = 'bar-label';
        label.textContent = item.label;
        label.style.cssText = `
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            color: #6b7280;
            white-space: nowrap;
        `;

        // Eventos hover
        bar.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.2)';
            tooltip.style.opacity = '1';
        });

        bar.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = 'none';
            tooltip.style.opacity = '0';
        });

        bar.appendChild(tooltip);
        bar.appendChild(value);
        bar.appendChild(label);
        barWrapper.appendChild(bar);
        container.appendChild(barWrapper);
    });
}

/**
 * Crear gráfico de líneas mejorado con SVG
 */
function createLineChart(svgId, data) {
    const svg = document.getElementById(svgId);
    if (!svg) return;

    // Limpiar SVG
    svg.innerHTML = '';

    const width = 400;
    const height = 200;
    const padding = 30;

    svg.setAttribute('viewBox', `0 0 ${width} ${height}`);

    // Calcular escalas
    const maxY = Math.max(...data.map(d => d.y));
    const minY = Math.min(...data.map(d => d.y));
    const rangeY = maxY - minY;
    const scaleX = (width - 2 * padding) / (data.length - 1);
    const scaleY = (height - 2 * padding) / rangeY;

    // Crear grupo principal
    const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');

    // Líneas de cuadrícula horizontales
    for (let i = 0; i <= 5; i++) {
        const y = padding + (i * (height - 2 * padding) / 5);
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
        line.setAttribute('x1', padding);
        line.setAttribute('y1', y);
        line.setAttribute('x2', width - padding);
        line.setAttribute('y2', y);
        line.setAttribute('stroke', '#e5e7eb');
        line.setAttribute('stroke-width', '0.5');
        line.setAttribute('stroke-dasharray', '2,2');
        g.appendChild(line);

        // Etiquetas del eje Y
        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        const value = Math.round(maxY - (i * rangeY / 5));
        text.setAttribute('x', padding - 5);
        text.setAttribute('y', y + 3);
        text.setAttribute('text-anchor', 'end');
        text.setAttribute('font-size', '10');
        text.setAttribute('fill', '#6b7280');
        text.textContent = value;
        g.appendChild(text);
    }

    // Crear gradiente para el área
    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
    gradient.setAttribute('id', 'areaGradient');
    gradient.setAttribute('x1', '0%');
    gradient.setAttribute('y1', '0%');
    gradient.setAttribute('x2', '0%');
    gradient.setAttribute('y2', '100%');

    const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
    stop1.setAttribute('offset', '0%');
    stop1.setAttribute('stop-color', '#67e8f9');
    stop1.setAttribute('stop-opacity', '0.3');

    const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
    stop2.setAttribute('offset', '100%');
    stop2.setAttribute('stop-color', '#67e8f9');
    stop2.setAttribute('stop-opacity', '0.05');

    gradient.appendChild(stop1);
    gradient.appendChild(stop2);
    defs.appendChild(gradient);
    svg.appendChild(defs);

    // Crear la línea y el área
    let pathData = '';
    let areaPathData = '';

    data.forEach((point, index) => {
        const x = padding + point.x * scaleX;
        const y = height - padding - ((point.y - minY) * scaleY);

        if (index === 0) {
            pathData += `M ${x} ${y}`;
            areaPathData += `M ${x} ${y}`;
        } else {
            // Curva suave
            const prevPoint = data[index - 1];
            const prevX = padding + prevPoint.x * scaleX;
            const prevY = height - padding - ((prevPoint.y - minY) * scaleY);
            const cpX = (prevX + x) / 2;

            pathData += ` Q ${cpX} ${prevY} ${x} ${y}`;
            areaPathData += ` Q ${cpX} ${prevY} ${x} ${y}`;
        }

        // Etiquetas del eje X
        if (point.label) {
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.setAttribute('x', x);
            text.setAttribute('y', height - padding + 15);
            text.setAttribute('text-anchor', 'middle');
            text.setAttribute('font-size', '10');
            text.setAttribute('fill', '#6b7280');
            text.textContent = point.label;
            g.appendChild(text);
        }
    });

    // Completar el área
    areaPathData += ` L ${width - padding} ${height - padding} L ${padding} ${height - padding} Z`;

    // Crear el área bajo la curva
    const areaPath = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    areaPath.setAttribute('d', areaPathData);
    areaPath.setAttribute('fill', 'url(#areaGradient)');
    g.appendChild(areaPath);

    // Crear la línea principal
    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', pathData);
    path.setAttribute('stroke', '#22d3ee');
    path.setAttribute('stroke-width', '2');
    path.setAttribute('fill', 'none');
    path.setAttribute('stroke-linecap', 'round');
    path.setAttribute('stroke-linejoin', 'round');

    // Animación de dibujo de línea
    const length = path.getTotalLength();
    path.style.strokeDasharray = length;
    path.style.strokeDashoffset = length;
    path.style.animation = 'drawLine 2s ease forwards';

    g.appendChild(path);

    // Agregar puntos interactivos
    data.forEach((point, index) => {
        const x = padding + point.x * scaleX;
        const y = height - padding - ((point.y - minY) * scaleY);

        // Círculo exterior
        const circleOuter = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circleOuter.setAttribute('cx', x);
        circleOuter.setAttribute('cy', y);
        circleOuter.setAttribute('r', '6');
        circleOuter.setAttribute('fill', '#fff');
        circleOuter.setAttribute('stroke', '#22d3ee');
        circleOuter.setAttribute('stroke-width', '2');
        circleOuter.style.cursor = 'pointer';
        circleOuter.style.opacity = '0';
        circleOuter.style.animation = `fadeIn 0.5s ease ${index * 0.1 + 1}s forwards`;

        // Círculo interior
        const circleInner = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
        circleInner.setAttribute('cx', x);
        circleInner.setAttribute('cy', y);
        circleInner.setAttribute('r', '3');
        circleInner.setAttribute('fill', '#22d3ee');

        // Tooltip
        const tooltip = document.createElementNS('http://www.w3.org/2000/svg', 'g');
        tooltip.style.opacity = '0';
        tooltip.style.transition = 'opacity 0.3s';

        const rect = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
        rect.setAttribute('x', x - 25);
        rect.setAttribute('y', y - 30);
        rect.setAttribute('width', '50');
        rect.setAttribute('height', '20');
        rect.setAttribute('rx', '3');
        rect.setAttribute('fill', '#1f2937');

        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.setAttribute('x', x);
        text.setAttribute('y', y - 15);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('font-size', '11');
        text.setAttribute('fill', '#fff');
        text.textContent = point.y;

        tooltip.appendChild(rect);
        tooltip.appendChild(text);

        // Eventos
        circleOuter.addEventListener('mouseenter', function() {
            this.setAttribute('r', '8');
            tooltip.style.opacity = '1';
        });

        circleOuter.addEventListener('mouseleave', function() {
            this.setAttribute('r', '6');
            tooltip.style.opacity = '0';
        });

        g.appendChild(circleOuter);
        g.appendChild(circleInner);
        g.appendChild(tooltip);
    });

    svg.appendChild(g);

    // Agregar estilos de animación
    const style = document.createElementNS('http://www.w3.org/2000/svg', 'style');
    style.textContent = `
        @keyframes drawLine {
            to { stroke-dashoffset: 0; }
        }
        @keyframes fadeIn {
            to { opacity: 1; }
        }
    `;
    svg.appendChild(style);
}

/**
 * Crear gráfico circular (pie chart)
 */
function createPieChart(containerId, data) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.innerHTML = '';

    const total = data.reduce((sum, item) => sum + item.value, 0);
    let currentAngle = -90; // Empezar desde arriba

    const size = 200;
    const center = size / 2;
    const radius = 80;

    // Crear SVG para el pie chart
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', size);
    svg.setAttribute('height', size);
    svg.setAttribute('viewBox', `0 0 ${size} ${size}`);

    data.forEach((item, index) => {
        const percentage = item.value / total;
        const angle = percentage * 360;

        // Crear sector
        const sector = createSector(center, center, radius, currentAngle, currentAngle + angle, item.color);

        // Animación de entrada
        sector.style.transform = 'scale(0)';
        sector.style.transformOrigin = 'center';
        sector.style.transition = `transform 0.5s ease ${index * 0.1}s`;

        setTimeout(() => {
            sector.style.transform = 'scale(1)';
        }, 50);

        // Hover effect
        sector.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
            this.style.filter = 'brightness(1.1)';
        });

        sector.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.filter = 'brightness(1)';
        });

        svg.appendChild(sector);

        // Agregar etiqueta
        const labelAngle = currentAngle + angle / 2;
        const labelX = center + Math.cos(labelAngle * Math.PI / 180) * (radius + 20);
        const labelY = center + Math.sin(labelAngle * Math.PI / 180) * (radius + 20);

        const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
        text.setAttribute('x', labelX);
        text.setAttribute('y', labelY);
        text.setAttribute('text-anchor', 'middle');
        text.setAttribute('font-size', '12');
        text.setAttribute('fill', '#374151');
        text.textContent = `${item.label} (${Math.round(percentage * 100)}%)`;
        svg.appendChild(text);

        currentAngle += angle;
    });

    container.appendChild(svg);

    // Agregar leyenda
    const legend = document.createElement('div');
    legend.style.cssText = 'margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px;';

    data.forEach(item => {
        const legendItem = document.createElement('div');
        legendItem.style.cssText = 'display: flex; align-items: center; gap: 5px;';

        const color = document.createElement('div');
        color.style.cssText = `width: 12px; height: 12px; background: ${item.color}; border-radius: 2px;`;

        const label = document.createElement('span');
        label.style.cssText = 'font-size: 12px; color: #6b7280;';
        label.textContent = item.label;

        legendItem.appendChild(color);
        legendItem.appendChild(label);
        legend.appendChild(legendItem);
    });

    container.appendChild(legend);
}

/**
 * Crear un sector del pie chart
 */
function createSector(cx, cy, r, startAngle, endAngle, color) {
    const largeArcFlag = endAngle - startAngle > 180 ? 1 : 0;

    const x1 = cx + r * Math.cos(startAngle * Math.PI / 180);
    const y1 = cy + r * Math.sin(startAngle * Math.PI / 180);
    const x2 = cx + r * Math.cos(endAngle * Math.PI / 180);
    const y2 = cy + r * Math.sin(endAngle * Math.PI / 180);

    const pathData = [
        `M ${cx} ${cy}`,
        `L ${x1} ${y1}`,
        `A ${r} ${r} 0 ${largeArcFlag} 1 ${x2} ${y2}`,
        'Z'
    ].join(' ');

    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    path.setAttribute('d', pathData);
    path.setAttribute('fill', color);
    path.style.cursor = 'pointer';
    path.style.transition = 'all 0.3s ease';

    return path;
}

/**
 * Ajustar brillo del color
 */
function adjustColor(color, percent) {
    const num = parseInt(color.replace('#', ''), 16);
    const amt = Math.round(2.55 * percent);
    const R = (num >> 16) + amt;
    const G = (num >> 8 & 0x00FF) + amt;
    const B = (num & 0x0000FF) + amt;
    return '#' + (0x1000000 + (R < 255 ? R < 1 ? 0 : R : 255) * 0x10000 +
        (G < 255 ? G < 1 ? 0 : G : 255) * 0x100 +
        (B < 255 ? B < 1 ? 0 : B : 255))
        .toString(16).slice(1);
}

/**
 * Animación al hacer scroll
 */
function animateOnScroll() {
    const elements = document.querySelectorAll('.bg-white');

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';

                setTimeout(() => {
                    entry.target.style.transition = 'all 0.5s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);

                observer.unobserve(entry.target);
            }
        });
    });

    elements.forEach(element => {
        observer.observe(element);
    });
}

/**
 * Función para actualizar datos dinámicamente
 */
function updateCharts() {
    // Generar nuevos datos aleatorios
    const newBarData = getBarChartData().map(item => ({
        ...item,
        value: Math.floor(Math.random() * 100) + 20
    }));

    const newLineData = getLineChartData().map(item => ({
        ...item,
        y: Math.floor(Math.random() * 80) + 20
    }));

    // Recrear gráficos con nuevos datos
    createBarChart('barChart', newBarData);
    createLineChart('lineChartSvg', newLineData);
}

// Exportar funciones para uso externo
window.ChartUtils = {
    createBarChart,
    createLineChart,
    createPieChart,
    updateCharts
};