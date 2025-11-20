<div class="min-vh-100">
    <h4 class="p-3 text-center fw-bold">Status Permohonan</h4>
    <div class="my-3">
        <div class="timeline" data-timeline-step="{{ $user_doc_status['st'] + 1 }}">
            <div class="timeline-item" data-status="waiting-verification">
                <div class="timeline-content">
                    <div class="timeline-marker"></div>
                    <div class="timeline-card">
                        <div class="timeline-header">
                            <i class="fas fa-clock text-danger"></i>
                            <h5 class="title">Menunggu Verifikasi</h5>
                        </div>
                        <div class="small">Permohonan baru dibuat.</div>
                        <div class="small text-muted">{{ $user_doc_status['tgl_input'] }}</div>
                    </div>
                </div>
            </div>

            <div class="timeline-item" data-status="waiting-info">
                <div class="timeline-content">
                    <div class="timeline-marker"></div>
                    <div class="timeline-card">
                        <div class="timeline-header">
                            <i class="fas fa-info-circle text-warning"></i>
                            <h5 class="title">Validasi Data</h5>
                        </div>
                        <div class="small">Sinkronisasi data pemohon.</div>
                    </div>
                </div>
            </div>

            <div class="timeline-item" data-status="processing">
                <div class="timeline-content">
                    <div class="timeline-marker"></div>
                    <div class="timeline-card">
                        <div class="timeline-header">
                            <i class="fas fa-cog text-primary"></i>
                            <h5 class="title">Dokumen Di Proses</h5>
                        </div>
                        <div class="small">Dokumen diproses sesuai data baru.</div>
                    </div>
                </div>
            </div>

            <div class="timeline-item" data-status="completed">
                <div class="timeline-content">
                    <div class="timeline-marker"></div>
                    <div class="timeline-card">
                        <div class="timeline-header">
                            <i class="fas fa-check-circle text-success"></i>
                            <h5 class="title">Dokumen Selesai</h5>
                        </div>
                        <div class="small">Dokumen telah selesai diproses dan dicetak.</div>
                        @if($user_doc_status['st'] == 3)
                        <div class="small text-muted">{{ $user_doc_status['tgl_proses'] }}</div>
                        @endif
                        <div class="notify small hidden alert alert-success mt-3">
                            <h6 class="alert-heading fw-bold mb-3">Informasi Pengambilan Dokumen:</h6>
                            <p class="mb-2">Bisa diambil ke Kantor Dukcapil Kab. Tapin (Lantai 2, Bidang Pemanfaatan
                                Data)</p>
                            <h6 class="fw-bold mb-2">Dokumen yang Perlu Dilampirkan (Asli bukan fotokopi):</h6>
                            <ol class="ps-3 mb-3">
                                <li>Kartu Keluarga Suami</li>
                                <li>Kartu Keluarga Istri</li>
                                <li>KTP-el Suami</li>
                                <li>KTP-el Istri</li>
                                <li>Ijazah Terakhir Suami & Istri</li>
                                <li>SK Kerja (Opsional)*</li>
                            </ol>
                            <p class="mb-0 fst-italic">*Untuk perubahan pekerjaan seperti: Karyawan Swasta, PNS dan lain
                                sebagainya WAJIB melampirkan SK.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const timeline = document.querySelector('.timeline');

        if (timeline && timeline.hasAttribute('data-timeline-step')) {
            const timelineStep = parseInt(timeline.getAttribute('data-timeline-step'));
            const timelineItems = timeline.querySelectorAll('.timeline-item');

            timelineItems.forEach((item, index) => {
                if (index < timelineStep) {
                    item.classList.add('active');
                    item.style.opacity = '1';
                } else {
                    item.classList.remove('active');
                    item.style.opacity = '0.6';
                }
            });

            if (timelineStep == 4) {
                document.querySelector('.notify').classList.remove('hidden');
            }
        } else {
            console.warn('Step tidak ditemukan.');
        }
    });

</script>
