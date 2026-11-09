const users = [
  { name: 'Nadia Rahman', path: '/billing', device: 'Desktop', status: 'online' },
  { name: 'James Carter', path: '/projects/42', device: 'Mobile', status: 'online' },
  { name: 'Guest', path: '/pricing', device: 'Tablet', status: 'idle' },
  { name: 'Ayesha Khan', path: '/settings', device: 'Desktop', status: 'online' },
  { name: 'Omar Ali', path: '/docs/api', device: 'Mobile', status: 'idle' },
  { name: 'Sophie Lee', path: '/dashboard', device: 'Desktop', status: 'online' },
];

const devices = [
  { label: 'Desktop', pct: 54 },
  { label: 'Mobile', pct: 38 },
  { label: 'Tablet', pct: 8 },
];

const usersEl = document.getElementById('users');
usersEl.innerHTML = users.map((u) => `
  <tr>
    <td><strong>${u.name}</strong><br><span style="color:#5b6b82;font-size:.8rem">${u.path}</span></td>
    <td>${u.device}</td>
    <td><span class="pill ${u.status}">${u.status[0].toUpperCase()}${u.status.slice(1)}</span></td>
  </tr>
`).join('');

document.getElementById('device-bars').innerHTML = devices.map((d) => `
  <div class="bar-row">
    <div>${d.label}</div>
    <div class="bar-track"><div class="bar-fill" style="width:${d.pct}%"></div></div>
    <div>${d.pct}%</div>
  </div>
`).join('');

const labels = Array.from({ length: 24 }, (_, i) => `${String(i).padStart(2, '0')}:00`);
const values = [42,38,31,28,26,33,58,96,140,168,190,210,204,198,186,172,165,158,149,132,110,88,70,54];

const ctx = document.getElementById('chart');
if (ctx && window.Chart) {
  new Chart(ctx, {
    type: 'line',
    data: {
      labels,
      datasets: [{
        data: values,
        borderColor: '#0f8f8a',
        backgroundColor: 'rgba(15,143,138,.12)',
        fill: true,
        tension: 0.35,
        pointRadius: 0,
        borderWidth: 2.5,
      }],
    },
    options: {
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, grid: { color: 'rgba(215,224,236,.8)' } },
      },
    },
  });
}

const active = document.getElementById('kpi-active');
setInterval(() => {
  const base = 1284;
  const next = base + Math.floor(Math.random() * 21) - 10;
  active.textContent = next.toLocaleString();
}, 4000);
