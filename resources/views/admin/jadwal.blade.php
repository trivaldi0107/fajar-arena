@extends('admin.layouts.app')

@section('title', 'Kelola Jadwal')

@section('content')

<div class="w-full">

    <!-- Header -->
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h2 class="text-3xl font-bold text-gray-800">Kelola Jadwal</h2>
        </div>
    </div>

    <!-- Box Kalender -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-8 mb-8 relative z-50">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <h3 class="text-xl font-bold text-gray-800">Pilih Tanggal</h3>
            <div class="flex gap-2">
                <button onclick="window.prevMonth()" class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-sm font-semibold">&larr; Sebelumnya</button>
                <button onclick="window.nextMonth()" class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-sm font-semibold">Berikutnya &rarr;</button>
            </div>
        </div>
        <div id="calendar-container" class="space-y-8"></div>
    </div>

    <!-- Box Jadwal -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 md:p-8 relative min-h-[400px]">
        <h3 class="text-lg font-bold text-gray-800 mb-6" id="table-title">Jadwal Lapangan</h3>
        
        <div id="loading-indicator" class="absolute inset-0 bg-white/70 flex items-center justify-center z-10 rounded-2xl">
            <div class="text-blue-600 font-bold">Memuat data...</div>
        </div>

        <div class="overflow-x-auto pb-6">
            <table class="w-full text-sm text-left whitespace-nowrap">
                <thead id="table-head"></thead>
                <tbody id="table-body" class="divide-y divide-gray-100 text-gray-700"></tbody>
            </table>
        </div>
    </div>

</div>

@push('modals')
<!-- Custom Confirm Modal -->
<div id="custom-confirm-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 md:p-0">
    <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity duration-300 opacity-0" id="custom-confirm-backdrop"></div>
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 relative z-10 transform scale-95 opacity-0 transition-all duration-300" id="custom-confirm-box">
        <div class="flex items-center gap-4 mb-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900 tracking-tight">Konfirmasi</h3>
                <p class="text-sm text-gray-500 mt-1" id="custom-confirm-message">Apakah Anda yakin?</p>
            </div>
        </div>
        <div class="flex justify-end gap-3 mt-8">
            <button id="custom-confirm-cancel" class="px-6 py-2.5 rounded-xl font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Batal</button>
            <button id="custom-confirm-ok" class="px-6 py-2.5 rounded-xl font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5">Ya, Ubah Status</button>
        </div>
        </div>
    </div>
</div>

<!-- Floating Action Bar for Bulk Update -->
<div id="bulk-action-bar" class="fixed bottom-6 left-1/2 lg:left-[calc(50%+9rem)] -translate-x-1/2 bg-white rounded-2xl md:rounded-full shadow-[0_10px_40px_rgba(0,0,0,0.15)] border border-gray-100 px-4 md:px-6 py-3 flex flex-col md:flex-row items-center gap-2 md:gap-4 z-[90] transition-all duration-300 translate-y-24 opacity-0 pointer-events-none w-[90%] md:w-auto">
    <span id="bulk-action-text" class="font-bold text-blue-600 text-sm md:text-base text-center">0 Tanggal Terpilih</span>
    <div class="hidden md:block w-px h-6 bg-gray-200"></div>
    <div class="flex items-center gap-2 w-full md:w-auto justify-center">
        <label class="text-xs font-bold text-gray-500 whitespace-nowrap">UBAH SEMUA :</label>
        
        <div class="relative w-full md:w-auto">
            <button id="bulk-action-btn" type="button" onclick="document.getElementById('bulk-action-menu').classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180');" class="flex items-center justify-between w-full md:w-48 px-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-xl hover:bg-gray-100 transition-colors shadow-sm hover:shadow">
                <span class="font-semibold text-gray-700">Pilih Status...</span>
                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div id="bulk-action-menu" class="absolute bottom-full left-0 mb-3 w-full md:w-56 bg-white border border-gray-100 rounded-xl shadow-2xl overflow-hidden flex-col z-[100] p-1.5 origin-bottom hidden transition-all">
                <button type="button" onclick="window.updateAllStatus('tersedia'); document.getElementById('bulk-action-menu').classList.add('hidden'); document.getElementById('bulk-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-blue-500 shadow-sm"></div>Tersedia</button>
                <button type="button" onclick="window.updateAllStatus('proses'); document.getElementById('bulk-action-menu').classList.add('hidden'); document.getElementById('bulk-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-orange-50 hover:text-orange-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-orange-500 shadow-sm"></div>Menunggu Pembayaran</button>
                <button type="button" onclick="window.updateAllStatus('berhasil'); document.getElementById('bulk-action-menu').classList.add('hidden'); document.getElementById('bulk-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-gray-500 shadow-sm"></div>Sudah Dipesan</button>
                <button type="button" onclick="window.updateAllStatus('diperbaiki'); document.getElementById('bulk-action-menu').classList.add('hidden'); document.getElementById('bulk-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-sm"></div>Diperbaiki</button>
                <button type="button" onclick="window.updateAllStatus('event'); document.getElementById('bulk-action-menu').classList.add('hidden'); document.getElementById('bulk-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-purple-500 shadow-sm"></div>Berlangsung Event</button>
                <button type="button" onclick="window.updateAllStatus('tutup'); document.getElementById('bulk-action-menu').classList.add('hidden'); document.getElementById('bulk-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 hover:text-gray-800 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-black shadow-sm"></div>Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Floating Action Bar for Slot Bulk Update -->
<div id="bulk-slot-action-bar" class="fixed bottom-6 left-1/2 lg:left-[calc(50%+9rem)] -translate-x-1/2 bg-white rounded-2xl md:rounded-full shadow-[0_10px_40px_rgba(59,130,246,0.2)] border border-blue-200 px-4 md:px-6 py-3 flex flex-col md:flex-row items-center gap-2 md:gap-4 z-[95] transition-all duration-300 translate-y-24 opacity-0 pointer-events-none ring-4 ring-blue-50 w-[90%] md:w-auto">
    <span id="bulk-slot-action-text" class="font-bold text-blue-600 text-sm md:text-base text-center">0 Jadwal Terpilih</span>
    <div class="hidden md:block w-px h-6 bg-blue-200"></div>
    <div class="flex items-center gap-2 w-full md:w-auto justify-center">
        <label class="text-xs font-bold text-blue-500 whitespace-nowrap">UBAH JADWAL :</label>
        
        <div class="relative w-full md:w-auto">
            <button id="bulk-slot-action-btn" type="button" onclick="document.getElementById('bulk-slot-action-menu').classList.toggle('hidden'); this.querySelector('svg').classList.toggle('rotate-180');" class="flex items-center justify-between w-full md:w-48 px-4 py-2 text-sm bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 transition-colors shadow-sm hover:shadow text-blue-700">
                <span class="font-semibold">Pilih Status...</span>
                <svg class="w-4 h-4 text-blue-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </button>
            <div id="bulk-slot-action-menu" class="absolute bottom-full left-0 mb-3 w-full md:w-56 bg-white border border-blue-100 rounded-xl shadow-2xl overflow-hidden flex-col z-[100] p-1.5 origin-bottom hidden transition-all">
                <button type="button" onclick="window.updateSlotsStatus('tersedia'); document.getElementById('bulk-slot-action-menu').classList.add('hidden'); document.getElementById('bulk-slot-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-blue-500 shadow-sm"></div>Tersedia</button>
                <button type="button" onclick="window.updateSlotsStatus('proses'); document.getElementById('bulk-slot-action-menu').classList.add('hidden'); document.getElementById('bulk-slot-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-orange-50 hover:text-orange-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-orange-500 shadow-sm"></div>Menunggu Pembayaran</button>
                <button type="button" onclick="window.updateSlotsStatus('berhasil'); document.getElementById('bulk-slot-action-menu').classList.add('hidden'); document.getElementById('bulk-slot-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 hover:text-gray-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-gray-500 shadow-sm"></div>Sudah Dipesan</button>
                <button type="button" onclick="window.updateSlotsStatus('diperbaiki'); document.getElementById('bulk-slot-action-menu').classList.add('hidden'); document.getElementById('bulk-slot-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-red-50 hover:text-red-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-red-500 shadow-sm"></div>Diperbaiki</button>
                <button type="button" onclick="window.updateSlotsStatus('event'); document.getElementById('bulk-slot-action-menu').classList.add('hidden'); document.getElementById('bulk-slot-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-purple-50 hover:text-purple-700 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-purple-500 shadow-sm"></div>Berlangsung Event</button>
                <button type="button" onclick="window.updateSlotsStatus('tutup'); document.getElementById('bulk-slot-action-menu').classList.add('hidden'); document.getElementById('bulk-slot-action-btn').querySelector('svg').classList.remove('rotate-180');" class="text-left w-full px-3 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 hover:text-gray-800 rounded-lg transition-colors flex items-center gap-3"><div class="w-2.5 h-2.5 rounded-full bg-black shadow-sm"></div>Tutup</button>
            </div>
        </div>
    </div>
</div>
@endpush

<script>
document.addEventListener('DOMContentLoaded', function() {
    let selectedDates = ['{{ date("Y-m-d") }}'];
    let activeDate = '{{ date("Y-m-d") }}';
    let jadwalData = [];
    let lapanganList = [];
    let selectedSlots = [];

    const calendarContainer = document.getElementById('calendar-container');
    const tableHead = document.getElementById('table-head');
    const tableBody = document.getElementById('table-body');
    const loadingIndicator = document.getElementById('loading-indicator');

    window.popupAllOpen = false;
    window.popupSlotId = null;
    window.currentMonthOffset = 0;
    window.isInitialCalendarLoad = true;
    window.isInitialTableLoad = true;

    function formatDateForDisplay(dateStr) {
        let d = new Date(dateStr);
        return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    window.prevMonth = function() {
        window.currentMonthOffset -= 1;
        window.generateCalendar();
    }

    window.nextMonth = function() {
        window.currentMonthOffset += 1;
        window.generateCalendar();
    }

    window.generateCalendar = function() {
        let html = '';
        let today = new Date();
        let currentMonth = today.getMonth();
        let currentYear = today.getFullYear();
        
        for(let i=0; i<2; i++) {
            let d = new Date(currentYear, currentMonth + window.currentMonthOffset + i, 1);
            let monthName = d.toLocaleString('id-ID', { month: 'long' });
            let year = d.getFullYear();
            let monthNum = d.getMonth();

            let daysInMonth = new Date(year, monthNum + 1, 0).getDate();
            let firstDayIndex = new Date(year, monthNum, 1).getDay();
            
            html += `
                <div class="mb-10 relative">
                    <div class="flex items-center justify-center gap-4 mb-6">
                        <div class="h-px bg-gradient-to-r from-transparent to-gray-200 flex-1"></div>
                        <h4 class="text-center font-bold text-xl text-gray-800 tracking-tight">${monthName} ${year}</h4>
                        <div class="h-px bg-gradient-to-l from-transparent to-gray-200 flex-1"></div>
                    </div>
                    <div class="grid grid-cols-7 gap-1 sm:gap-4 mb-3 text-center text-[10px] sm:text-xs font-bold text-gray-400 uppercase tracking-widest">
                        <div>Min</div><div>Sen</div><div>Sel</div><div>Rab</div><div>Kam</div><div>Jum</div><div>Sab</div>
                    </div>
                    <div class="grid grid-cols-7 gap-1 sm:gap-4">`;

            for(let j=0; j<firstDayIndex; j++) {
                html += `<div></div>`;
            }

            for(let day=1; day<=daysInMonth; day++) {
                let dateStr = year + '-' + String(monthNum+1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
                let monthShort = d.toLocaleString('en-US', { month: 'short' }).toUpperCase();
                
                let isSelected = selectedDates.includes(dateStr);
                
                let btnClass = isSelected 
                    ? 'bg-gradient-to-br from-blue-600 to-indigo-600 text-white border-transparent shadow-lg shadow-blue-500/30 scale-[1.02] z-20' 
                    : 'bg-white border-gray-100 text-gray-700 shadow-sm hover:shadow-md hover:-translate-y-1 hover:border-blue-200';
                
                let numClass = isSelected ? 'text-white' : 'text-gray-800';
                let textClass = isSelected ? 'text-blue-100' : 'text-gray-400';

                let revealClass = window.isInitialCalendarLoad ? 'scroll-reveal' : '';
                
                html += `
                    <div class="relative group ${revealClass}">
                        <button onclick="handleDateClick('${dateStr}')" 
                            class="w-full p-2 sm:p-3 md:p-4 rounded-xl md:rounded-2xl flex flex-col items-center justify-center transition-all duration-300 border ${btnClass}">
                            <span class="text-base sm:text-lg md:text-xl font-bold tracking-tight ${numClass}">${day}</span>
                            <span class="text-[10px] sm:text-[11px] md:text-xs font-medium uppercase tracking-wider mt-1 ${textClass}">${monthShort}</span>
                        </button>
                    </div>`;
            }
            
            html += `</div></div>`;
        }
        
        calendarContainer.innerHTML = html;
        if(window.applyScrollReveal) window.applyScrollReveal();
        window.isInitialCalendarLoad = false;
    }

    window.handleDateClick = function(dateStr) {
        let idx = selectedDates.indexOf(dateStr);
        if (idx > -1) {
            selectedDates.splice(idx, 1);
        } else {
            selectedDates.push(dateStr);
        }
        
        if (selectedDates.length > 0) {
            activeDate = selectedDates[selectedDates.length - 1];
        } else {
            activeDate = null;
        }

        window.updateFloatingBar();
        window.generateCalendar();
        
        if (activeDate) {
            window.fetchJadwal();
        } else {
            jadwalData = [];
            window.renderTable();
        }
    }

    window.updateFloatingBar = function() {
        const bar = document.getElementById('bulk-action-bar');
        const text = document.getElementById('bulk-action-text');
        
        if (selectedDates.length > 0) {
            if (selectedDates.length === 1) {
                text.innerText = formatDateForDisplay(selectedDates[0]);
            } else {
                text.innerText = selectedDates.length + ' Tanggal Terpilih';
            }
            bar.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            bar.classList.add('translate-y-0', 'opacity-100');
        } else {
            bar.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
            bar.classList.remove('translate-y-0', 'opacity-100');
        }
    }

    window.fetchJadwal = function() {
        if (!activeDate) return;
        loadingIndicator.style.display = 'flex';
        
        selectedSlots = [];
        window.updateFloatingBarSlots();
        
        fetch('/admin/jadwal/data?tanggal=' + activeDate + '&_t=' + new Date().getTime())
            .then(r => r.json())
            .then(data => {
                lapanganList = data.lapangan;
                jadwalData = data.jadwal;
                window.renderTable();
                loadingIndicator.style.display = 'none';
            })
            .catch(err => {
                console.error(err);
                loadingIndicator.style.display = 'none';
            });
    }

    window.renderTable = function() {
        document.getElementById('table-title').innerText = activeDate ? 'Jadwal Lapangan - ' + formatDateForDisplay(activeDate) : 'Jadwal Lapangan';
        
        let thead = `<tr class="text-gray-500"><th class="pb-4 font-normal w-24 md:w-32 whitespace-nowrap sticky left-0 bg-white z-20 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.1)] px-2">Waktu</th>`;
        lapanganList.forEach((lap, idx) => {
            thead += `<th class="pb-4 font-normal text-center align-middle min-w-[140px]">
                <button type="button" onclick="window.selectAllForLapangan(${idx})" title="Pilih semua jadwal di ${lap}" class="font-bold text-gray-700 hover:text-blue-600 transition-colors inline-flex items-center gap-1 group">
                    ${lap}
                    <svg class="w-3.5 h-3.5 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </button>
            </th>`;
        });
        thead += `</tr>`;
        tableHead.innerHTML = thead;

        let tbody = '';
        
        if (jadwalData.length === 0) {
            tbody = `<tr><td colspan="${lapanganList.length + 1}" class="py-8 text-center text-gray-500 italic">Pilih tanggal untuk melihat jadwal.</td></tr>`;
        } else {
            jadwalData.forEach(row => {
            tbody += `<tr><td class="py-3 px-2 font-semibold sticky left-0 bg-white z-10 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)] whitespace-nowrap">${row.waktu}</td>`;
            row.slots.forEach(slot => {
                let statusClass = '';
                let displayStatus = slot.display_status === 'event' ? 'Event' : slot.display_status;
                let cursorClass = 'cursor-pointer hover:shadow-md hover:-translate-y-0.5';
                
                let isSlotSelected = selectedSlots.includes(slot.id);
                let selectionClass = isSlotSelected ? 'ring-2 ring-blue-600 ring-offset-2 shadow-md z-10' : '';

                if (slot.display_status === 'tersedia') {
                    statusClass = 'bg-blue-100 text-blue-600 font-medium border border-transparent hover:bg-blue-200';
                } else if (slot.display_status === 'waktu habis') {
                    statusClass = 'bg-gradient-to-br from-white to-gray-100 text-gray-400 font-medium border border-gray-200 opacity-80';
                    displayStatus = 'Waktu Habis';
                } else if (slot.display_status === 'sudah dipesan') {
                    statusClass = 'bg-gray-100 text-gray-400 font-medium border border-gray-100';
                    displayStatus = 'Sudah Dipesan';
                } else if (slot.display_status === 'proses') {
                    statusClass = 'bg-orange-50 text-orange-600 font-medium border border-orange-100 shadow-sm'; displayStatus = 'Menunggu';
                } else if (slot.display_status === 'diperbaiki') {
                    statusClass = 'bg-red-50 text-red-500 font-medium border border-red-100 hover:bg-red-100';
                } else if (slot.display_status === 'event') {
                    statusClass = 'bg-purple-50 text-purple-600 font-medium border border-purple-100 hover:bg-purple-100';
                } else if (slot.display_status === 'tutup') {
                    statusClass = 'bg-black text-white font-medium border border-black hover:bg-neutral-800';
                }

                let tableRevealClass = window.isInitialTableLoad ? 'scroll-reveal' : '';

                tbody += `<td class="py-3 px-2 text-center relative">
                    <button onclick="window.handleSlotClick(${slot.id}, ${slot.is_booked})" class="w-full py-2 rounded-full text-[13px] md:text-sm capitalize transition-all duration-300 ${tableRevealClass} ${statusClass} ${cursorClass} ${selectionClass}">
                        ${displayStatus}
                    </button>
                </td>`;
            });
            tbody += `</tr>`;
            });
        }
        tableBody.innerHTML = tbody;
        if(window.applyScrollReveal) window.applyScrollReveal();
        window.isInitialTableLoad = false;
    }

    window.handleSlotClick = function(id, is_booked) {
        
        let idx = selectedSlots.indexOf(id);
        if (idx > -1) {
            selectedSlots.splice(idx, 1);
        } else {
            selectedSlots.push(id);
        }
        
        window.renderTable();
        window.updateFloatingBarSlots();
    }

    window.selectAllForLapangan = function(idx) {
        let toSelect = [];
        jadwalData.forEach(row => {
            let slot = row.slots[idx];
            if (slot) {
                toSelect.push(slot.id);
            }
        });
        
        let allSelected = toSelect.every(id => selectedSlots.includes(id));
        
        if (allSelected) {
            // Deselect all for this column
            selectedSlots = selectedSlots.filter(id => !toSelect.includes(id));
        } else {
            // Select all for this column
            toSelect.forEach(id => {
                if (!selectedSlots.includes(id)) {
                    selectedSlots.push(id);
                }
            });
        }
        
        window.renderTable();
        window.updateFloatingBarSlots();
    }

    window.updateFloatingBarSlots = function() {
        const bar = document.getElementById('bulk-slot-action-bar');
        const text = document.getElementById('bulk-slot-action-text');
        
        if (selectedSlots.length > 0) {
            text.innerText = selectedSlots.length + ' Jadwal Terpilih';
            bar.classList.remove('translate-y-24', 'opacity-0', 'pointer-events-none');
            bar.classList.add('translate-y-0', 'opacity-100');
            // Hide the date action bar if it's visible, to prevent overlap
            document.getElementById('bulk-action-bar').classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
            document.getElementById('bulk-action-bar').classList.remove('translate-y-0', 'opacity-100');
        } else {
            bar.classList.add('translate-y-24', 'opacity-0', 'pointer-events-none');
            bar.classList.remove('translate-y-0', 'opacity-100');
            // Restore date action bar if needed
            window.updateFloatingBar();
        }
    }

    window.confirmModal = function(message, onConfirm, onCancel) {
        const modal = document.getElementById('custom-confirm-modal');
        const backdrop = document.getElementById('custom-confirm-backdrop');
        const box = document.getElementById('custom-confirm-box');
        const msgEl = document.getElementById('custom-confirm-message');
        const btnOk = document.getElementById('custom-confirm-ok');
        const btnCancel = document.getElementById('custom-confirm-cancel');

        msgEl.innerText = message;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        }, 10);

        const close = (isConfirm) => {
            backdrop.classList.add('opacity-0');
            box.classList.remove('scale-100', 'opacity-100');
            box.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                if(isConfirm && onConfirm) onConfirm();
                else if(!isConfirm && onCancel) onCancel();
            }, 300);
            btnOk.onclick = null;
            btnCancel.onclick = null;
        };

        btnOk.onclick = () => close(true);
        btnCancel.onclick = () => close(false);
    };

    window.updateAllStatus = function(status) {
        if (!status) return;
        if (selectedDates.length === 0) return;
        
        window.confirmModal('Apakah Anda yakin ingin mengubah seluruh jadwal pada ' + selectedDates.length + ' tanggal terpilih menjadi "' + status.toUpperCase() + '"?',
        function() {
            loadingIndicator.style.display = 'flex';
            fetch('/admin/jadwal/update-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tanggalList: selectedDates,
                    status: status
                })
            })
            .then(r => r.json())
            .then(() => {
                if(document.getElementById('bulk-action-select')) document.getElementById('bulk-action-select').value = '';
                if(activeDate) window.fetchJadwal();
            });
        },
        function() {
            if(document.getElementById('bulk-action-select')) document.getElementById('bulk-action-select').value = '';
        });
    }

    window.updateSlotsStatus = function(status) {
        if (!status) return;
        if (selectedSlots.length === 0) return;
        
        window.confirmModal('Apakah Anda yakin ingin mengubah status ' + selectedSlots.length + ' jadwal lapangan ini menjadi "' + status.toUpperCase() + '"?',
        function() {
            loadingIndicator.style.display = 'flex';
            fetch('/admin/jadwal/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ids: selectedSlots,
                    status: status
                })
            })
            .then(r => r.json())
            .then(() => {
                if(document.getElementById('bulk-slot-action-select')) document.getElementById('bulk-slot-action-select').value = '';
                if(activeDate) window.fetchJadwal();
            });
        },
        function() {
            if(document.getElementById('bulk-slot-action-select')) document.getElementById('bulk-slot-action-select').value = '';
        });
    }

    window.applyScrollReveal = function() {
        let delay = 0;
        let timer = null;
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    setTimeout(() => {
                        entry.target.classList.add('is-revealed');
                    }, delay);
                    delay += 50; 
                    if(timer) clearTimeout(timer);
                    timer = setTimeout(() => { delay = 0; }, 100);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: "0px 0px -20px 0px" });

        document.querySelectorAll('.scroll-reveal:not(.is-revealed)').forEach(el => observer.observe(el));
    };

    // Initialize
    window.updateFloatingBar();
    window.generateCalendar();
    window.fetchJadwal();
});

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const actionMenu = document.getElementById('bulk-action-menu');
    const actionBtn = document.getElementById('bulk-action-btn');
    if (actionMenu && !actionMenu.classList.contains('hidden') && !e.target.closest('#bulk-action-btn') && !e.target.closest('#bulk-action-menu')) {
        actionMenu.classList.add('hidden');
        if (actionBtn) actionBtn.querySelector('svg').classList.remove('rotate-180');
    }
    
    const slotMenu = document.getElementById('bulk-slot-action-menu');
    const slotBtn = document.getElementById('bulk-slot-action-btn');
    if (slotMenu && !slotMenu.classList.contains('hidden') && !e.target.closest('#bulk-slot-action-btn') && !e.target.closest('#bulk-slot-action-menu')) {
        slotMenu.classList.add('hidden');
        if (slotBtn) slotBtn.querySelector('svg').classList.remove('rotate-180');
    }
});
</script>

<style>
.scroll-reveal {
    opacity: 0;
    transform: translateY(20px) scale(0.98);
    transition: all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}
.scroll-reveal.is-revealed {
    opacity: 1;
    transform: translateY(0) scale(1);
}
</style>
@endsection

