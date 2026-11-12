// ...new file...
(function(){
    const data = window.__analytics || {};
    if (!data) return;

    // Monthly chart
    try {
        const ctxM = document.getElementById('monthlyChart');
        if (ctxM) {
            new Chart(ctxM.getContext('2d'), {
                type: 'line',
                data: {
                    labels: data.monthly.labels || [],
                    datasets: [{
                        label: 'Bookings',
                        data: data.monthly.counts || [],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59,130,246,0.12)',
                        fill: true,
                        tension: 0.2,
                        pointRadius: 4
                    }]
                },
                options: { responsive: true, plugins:{legend:{display:false}} }
            });
        }

        // By-route chart
        const ctxR = document.getElementById('byRouteChart');
        if (ctxR) {
            const labels = (data.bookingsByRoute||[]).map(r=>r.label);
            const counts = (data.bookingsByRoute||[]).map(r=>r.count);
            new Chart(ctxR.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Bookings (30d)',
                        data: counts,
                        backgroundColor: 'rgba(34,197,94,0.9)'
                    }]
                },
                options: { responsive:true, plugins:{legend:{display:false}}, scales:{x:{ticks:{autoSkip:false}}} }
            });

            // populate top routes list
            const topRoutes = document.getElementById('topRoutes');
            if (topRoutes) {
                topRoutes.innerHTML = (data.bookingsByRoute||[]).map(r=>`<li style="padding:6px 0;border-bottom:1px solid rgba(0,0,0,0.04)"><strong>${escapeHtml(r.label)}</strong><div class="muted" style="font-size:0.9rem">${r.count} bookings</div></li>`).join('');
            }
        }

        // recent bookings table
        const tbody = document.getElementById('recentBookingsBody');
        if (tbody && Array.isArray(data.recentBookings)) {
            tbody.innerHTML = data.recentBookings.map(rb=>{
                const when = rb.booked_at ? (new Date(rb.booked_at)).toLocaleString() : '';
                return `<tr>
                    <td style="padding:6px 4px">${escapeHtml(when)}</td>
                    <td style="padding:6px 4px">${escapeHtml(rb.passenger_name||'—')}</td>
                    <td style="padding:6px 4px">${escapeHtml(rb.route_name||'—')}</td>
                    <td style="padding:6px 4px">${escapeHtml(Number(rb.amount||0).toFixed(2))}</td>
                    <td style="padding:6px 4px">${escapeHtml(rb.status||'—')}</td>
                </tr>`;
            }).join('');
        }
    } catch (err) {
        // fail silently in dashboard
        console.error('analytics init error', err);
    }

    function escapeHtml(s){ return String(s||'').replace(/[&<>"']/g, function(m){ return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]; }); }
})();