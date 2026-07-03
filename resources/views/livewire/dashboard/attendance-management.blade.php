<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            
            <div class="p-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
                    <li class="me-2">
                        <button wire:click="setTab('input')" 
                            class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition-all {{ $tab == 'input' ? 'text-purple-600 border-purple-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="bi bi-calendar-check me-2"></i> Input Absensi
                        </button>
                    </li>
                    <li class="me-2">
                        <button wire:click="setTab('report')" 
                            class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition-all {{ $tab == 'report' ? 'text-purple-600 border-purple-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="bi bi-journal-text me-2"></i> Laporan & Gaji
                        </button>
                    </li>
                    <li class="me-2">
                        <button wire:click="setTab('setting')" 
                            class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition-all {{ $tab == 'setting' ? 'text-purple-600 border-purple-600' : 'border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="bi bi-gear me-2"></i> Pengaturan
                        </button>
                    </li>
                </ul>
            </div>

            <div class="p-6">
                <!-- INPUT TAB -->
                @if($tab == 'input')
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Input Kehadiran Karyawan</h3>
                            <p class="text-sm text-gray-500">Pilih tanggal dan tentukan status kehadiran masing-masing pegawai.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <label class="text-sm font-medium text-gray-700">Tanggal:</label>
                            <input type="date" wire:model.live="currentDate"
                                max="{{ date('Y-m-d') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 focus:border-purple-500 p-2.5 shadow-sm">
                        </div>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-gray-100">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-white uppercase bg-gray-500">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Pegawai</th>
                                    <th scope="col" class="px-6 py-3">Potongan Gaji</th>
                                    <th scope="col" class="px-6 py-3 text-center">Status Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody>
                            @forelse($this->activeEmployees as $emp)
                            <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $emp->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $emp->position ?? 'Karyawan' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-red-500 font-medium">Rp {{ number_format($emp->deduction_per_day, 0, ',', '.') }}</span>
                                    <span class="text-xs text-gray-400">/ hari</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        <select wire:model="attendances.{{ $emp->id }}" 
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-xs rounded-lg focus:ring-purple-500 focus:border-purple-500 block w-full max-w-[180px] p-2 transaction-all">
                                            <option value="present">✅ Hadir</option>
                                            <option value="absent">❌ Tidak Hadir (Potong)</option>
                                            <option value="leave">📝 Izin / Sakit</option>
                                            <option value="holiday">🏖️ Hari Libur</option>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-10 text-center text-gray-500">
                                    Data karyawan tidak ditemukan.
                                </td>
                            </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-center">
                        <button wire:click="saveAttendances" wire:loading.attr="disabled"
                            class="text-white bg-purple-500 hover:bg-purple-600 focus:ring-4 focus:outline-none focus:ring-purple-200 font-medium rounded-lg text-sm px-8 py-3 text-center transition-all shadow-lg shadow-purple-200">
                            <span wire:loading.remove wire:target="saveAttendances">Simpan Absensi</span>
                            <span wire:loading wire:target="saveAttendances">Menyimpan...</span>
                        </button>
                    </div>
                </div>
                @endif

                <!-- REPORT TAB -->
                @if($tab == 'report')
                <div class="space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Rekapitulasi Gaji & Absensi</h3>
                            <p class="text-sm text-gray-500">Perhitungan total gaji bersih berdasarkan jumlah ketidakhadiran.</p>
                        </div>
                        <div class="flex items-center gap-2 bg-gray-50 p-2 rounded-lg border border-gray-200">
                            <select wire:model.live="reportMonth" class="bg-white border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 p-2 min-w-[120px]">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                            <input type="number" wire:model.live="reportYear" class="bg-white border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-purple-500 p-2 w-24">
                        </div>
                    </div>

                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg border border-gray-100">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-white uppercase bg-gray-500">
                                <tr>
                                    <th scope="col" class="px-6 py-3">Nama Pegawai / Posisi</th>
                                    <th scope="col" class="px-6 py-3">Gaji Pokok</th>
                                    <th scope="col" class="px-6 py-3">Hadir</th>
                                    <th scope="col" class="px-6 py-3">Alpha</th>
                                    <th scope="col" class="px-6 py-3 text-red-500">Total Potongan</th>
                                    <th scope="col" class="px-6 py-3 text-green-600 bg-green-50/50">Gaji Bersih</th>
                                    <th scope="col" class="px-6 py-3 text-center rounded-tr-lg">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->reportData as $data)
                                <tr class="bg-white border-b hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-900">{{ $data['employee']->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $data['employee']->position ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">Rp {{ number_format($data['base_salary'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $data['presents'] }} Hari
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            {{ $data['absences'] }} Hari
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-red-600 font-bold">- Rp {{ number_format($data['deduction'], 0, ',', '.') }}</td>
                                    <td class="px-6 py-4 font-extrabold text-green-600 bg-green-50/30">
                                        Rp {{ number_format($data['total_salary'], 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-center border-l border-gray-50">
                                        <button wire:click="viewDetails({{ $data['employee']->id }})" class="text-white bg-purple-500 hover:bg-purple-600 px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-purple-200 transition-all flex items-center justify-center gap-1.5 mx-auto focus:ring-4 focus:ring-purple-100">
                                            <i class="bi bi-calendar-range"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-xs text-gray-400 italic">
                        * Gaji bersih dihitung dari Gaji Pokok dikurangi (Hari Tidak Hadir x Potongan per Hari).
                    </div>
                </div>
                @endif

                <!-- SETTING TAB -->
                @if($tab == 'setting')
                <div class="max-w-2xl mx-auto py-4">
                    <div class="bg-gray-50 rounded-2xl p-8 border border-gray-200">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                <i class="bi bi-calendar-x"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">Pengaturan Libur Mingguan</h3>
                        </div>
                        
                        <p class="text-sm text-gray-600 mb-8 leading-relaxed">
                            Pilih hari yang merupakan libur tetap. Pada hari ini, sistem absensi akan otomatis mengisi status pegawai sebagai <span class="font-bold text-purple-600">Hari Libur</span> dan tidak ada pemotongan gaji.
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                            @foreach($availableDays as $val => $day)
                            <label class="flex items-center p-4 bg-white rounded-xl border border-gray-200 cursor-pointer hover:border-purple-300 hover:bg-purple-50 transition-all group">
                                <input type="checkbox" wire:model="offDays" value="{{ $val }}" 
                                    class="w-5 h-5 text-purple-600 bg-gray-50 border-gray-300 rounded focus:ring-purple-500">
                                <span class="ms-3 text-sm font-semibold text-gray-700 group-hover:text-purple-700">{{ $day }}</span>
                            </label>
                            @endforeach
                        </div>
                        
                        <div class="flex justify-center mt-6">
                            <button wire:click="saveSettings" wire:loading.attr="disabled"
                                class="text-white bg-purple-600 hover:bg-purple-700 focus:ring-4 focus:ring-purple-300 font-bold rounded-xl text-sm px-10 py-3.5 transition-all shadow-xl shadow-purple-100">
                                <span wire:loading.remove wire:target="saveSettings">Simpan Pengaturan</span>
                                <span wire:loading wire:target="saveSettings">Menyimpan...</span>
                            </button>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- DETAIL MODAL -->
    @if($showDetailModal && $detailEmployee)
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all duration-300 scale-100">
            <div class="flex justify-between items-center px-6 py-5 border-b border-gray-100 bg-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 text-xl font-bold shadow-inner">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-gray-900 tracking-tight">Detail Kehadiran</h3>
                        <p class="text-sm font-medium text-purple-600">{{ $detailEmployee->name }} <span class="text-gray-400 mx-1">•</span> Bulan {{ $reportMonth }}/{{ $reportYear }}</p>
                    </div>
                </div>
                <button wire:click="closeDetailModal" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all">
                    <i class="bi bi-x-lg text-lg"></i>
                </button>
            </div>
            
            <div class="p-6 overflow-y-auto max-h-[70vh] bg-gray-50/50">
                <div class="mb-5 flex flex-wrap gap-2 sm:gap-3 justify-center text-[10px] sm:text-xs font-semibold text-gray-600">
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm"><div class="w-3 h-3 rounded-full" style="background:#22c55e"></div> Hadir</div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm"><div class="w-3 h-3 rounded-full" style="background:#ef4444"></div> Alpha</div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm"><div class="w-3 h-3 rounded-full" style="background:#3b82f6"></div> Hari Libur</div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm"><div class="w-3 h-3 rounded-full" style="background:#8b5cf6"></div> Izin/Sakit</div>
                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-white rounded-lg border border-gray-100 shadow-sm"><div class="w-3 h-3 rounded-full bg-gray-200"></div> Belum Input/Akan Datang</div>
                </div>

                <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
                    @foreach(['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $d)
                    <div class="text-center font-bold text-gray-400 text-[10px] sm:text-xs py-1 uppercase tracking-wider">{{ $d }}</div>
                    @endforeach

                    @php 
                        $firstDayOfWeek = \Carbon\Carbon::create($reportYear, $reportMonth, 1)->dayOfWeek;
                    @endphp
                    @for($i = 0; $i < $firstDayOfWeek; $i++)
                    <div class="aspect-square bg-transparent"></div>
                    @endfor

                    @foreach($daysInMonth as $dm)
                        @php $status = $detailAttendances[$dm['date']] ?? 'unrecorded'; @endphp
                        
                        @if($status == 'present')
                            <div class="aspect-square rounded-[0.8rem] border-0 flex flex-col items-center justify-center text-white transition-transform hover:-translate-y-0.5 hover:shadow-lg cursor-default relative overflow-hidden group shadow-md"
                                style="background: linear-gradient(135deg, #4ade80, #22c55e); box-shadow: 0 4px 12px rgba(34,197,94,0.35);">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                                <span class="text-sm sm:text-base font-black z-10">{{ $dm['day'] }}</span>
                                <i class="bi bi-check-lg text-xs z-10 opacity-90"></i>
                            </div>
                        @elseif($status == 'absent')
                            <div class="aspect-square rounded-[0.8rem] border-0 flex flex-col items-center justify-center text-white transition-transform hover:-translate-y-0.5 hover:shadow-lg cursor-default relative overflow-hidden group shadow-md"
                                style="background: linear-gradient(135deg, #f87171, #ef4444); box-shadow: 0 4px 12px rgba(239,68,68,0.35);">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                                <span class="text-sm sm:text-base font-black z-10">{{ $dm['day'] }}</span>
                                <i class="bi bi-x-lg text-xs z-10 opacity-90"></i>
                            </div>
                        @elseif($status == 'holiday')
                            <div class="aspect-square rounded-[0.8rem] border-0 flex flex-col items-center justify-center text-white transition-transform hover:-translate-y-0.5 hover:shadow-lg cursor-default relative overflow-hidden group shadow-md"
                                style="background: linear-gradient(135deg, #60a5fa, #3b82f6); box-shadow: 0 4px 12px rgba(59,130,246,0.35);">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                                <span class="text-sm sm:text-base font-black z-10">{{ $dm['day'] }}</span>
                                <i class="bi bi-cup-hot text-[10px] z-10 opacity-90"></i>
                            </div>
                        @elseif($status == 'leave')
                            <div class="aspect-square rounded-[0.8rem] border-0 flex flex-col items-center justify-center text-white transition-transform hover:-translate-y-0.5 hover:shadow-lg cursor-default relative overflow-hidden group shadow-md"
                                style="background: linear-gradient(135deg, #a78bfa, #8b5cf6); box-shadow: 0 4px 12px rgba(139,92,246,0.35);">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity"></div>
                                <span class="text-sm sm:text-base font-black z-10">{{ $dm['day'] }}</span>
                                <i class="bi bi-envelope-paper text-[10px] z-10 opacity-90"></i>
                            </div>
                        @elseif($status == 'future')
                            <div class="aspect-square rounded-[0.8rem] border border-dashed border-gray-200 flex flex-col items-center justify-center text-gray-300 cursor-default"
                                style="background: rgba(249,250,251,0.5);">
                                <span class="text-sm sm:text-base font-black">{{ $dm['day'] }}</span>
                            </div>
                        @else
                            {{-- unrecorded --}}
                            <div class="aspect-square rounded-[0.8rem] border border-gray-200 flex flex-col items-center justify-center text-gray-400 cursor-default shadow-sm bg-white">
                                <span class="text-sm sm:text-base font-black">{{ $dm['day'] }}</span>
                                <i class="bi bi-dash text-xs opacity-50"></i>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-100 bg-white flex justify-end">
                <button wire:click="closeDetailModal" class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-bold transition-colors focus:ring-4 focus:ring-gray-200">
                    Tutup Detail
                </button>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:navigated', () => {
            Livewire.on('savedSuccess', (event) => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
                Toast.fire({
                    icon: "success",
                    title: event.message
                });
            });
        });
    </script>
</div>
