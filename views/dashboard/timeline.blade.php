<div class="container">
    <div class="min-vh-100">
        <div class="p-3 fs-4 fw-bold">Status Permohonan</div>
        @if ($user_product)
        <div class="my-3">
            <div class="timeline" data-timeline-step="{{ $user_product['st'] + 1 }}">
                <div class="timeline-item" data-status="waiting-verification">
                    <div class="timeline-content">
                        <div class="timeline-marker"></div>
                        <div class="timeline-card">
                            <div class="timeline-header">
                                <i class="fas fa-clock text-danger"></i>
                                <h5 class="title">Menunggu Verifikasi</h5>
                            </div>
                            <div class="small">Permohonan baru dibuat.</div>
                            <div class="small text-muted">{{ $user_product['tgl_input'] }}</div>
                        </div>
                        @if($user_product['st'] == 0)
                        <div class="mt-3">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#viewData"><i class="bi bi-eye me-1"></i> Lihat</button>
                            <button id="btn-change-data" class="btn btn-success"><i class="bi bi-pencil-square me-1"></i> Ubah</button>
                        </div>
                        <div class="modal fade" id="viewData" tabindex="-1" aria-labelledby="viewDataLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <div class="modal-title" id="viewDataLabel">Formulir Permohonan</div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table  table-bordered small">
                                            <tr>
                                                <td colspan="2" class="fw-bold">
                                                    Data Laki-laki
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Nama Lengkap</th>
                                                <td>{{ $user_product['lk_nama'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>No. Kartu Keluarga</th>
                                                <td>{{ $user_product['lk_kk'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>NIK</th>
                                                <td>{{ $user_product['lk_nik'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Alamat</th>
                                                <td>{{ $user_product['lk_alamat'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>No. Telepon</th>
                                                <td>{{ $user_product['lk_telp'] }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2" class="fw-bold">
                                                    Data Perempuan
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Nama Lengkap</th>
                                                <td>{{ $user_product['pr_nama'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>No. Kartu Keluarga</th>
                                                <td>{{ $user_product['pr_kk'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>NIK</th>
                                                <td>{{ $user_product['pr_nik'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Alamat</th>
                                                <td>{{ $user_product['pr_alamat'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>No. Telepon</th>
                                                <td>{{ $user_product['pr_telp'] }}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <th>Alamat Baru</th>
                                                <td>{{ $user_product['alamat_baru'] }}</td>
                                            </tr>
                                            <tr>
                                                <th>Catatan</th>
                                                <td>{{ $user_product['keterangan'] }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-success" data-bs-dismiss="modal"><i class="bi bi-check-all me-1"></i> Konfirmasi</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
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
                            @if($user_product['st'] == 3)
                            <div class="small text-muted">{{ $user_product['tgl_proses'] }}</div>
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
        @else
        <div class="my-3">
            <div class="alert alert-warning">Mohon maaf data tidak ditemukan, silahkan coba kembali atau hubungi admin.</div>
        </div>
        @endif
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
                    item.style.opacity = '0.2';
                }
            });

            if (timelineStep == 4) {
                document.querySelector('.notify').classList.remove('hidden');
            }
        } else {
            console.warn('Step tidak ditemukan.');
        }
    });

    $("#btn-change-data").click(function() {
        Swal.fire({
            text: "Apakah kamu yakin ingin mengubah kembali data sebelumnya?"
            , icon: "warning"
            , showCancelButton: true
            , confirmButtonColor: "#3085d6"
            , cancelButtonColor: "#d33"
            , confirmButtonText: "Ya, ubah data!"
            , cancelButtonText: "Batal"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "/product/edit"
                    , type: "POST"
                    , success: function(response) {
                        if (response.status == "success") {
                            Swal.fire({
                                text: "Silahkan tunggu, memuat data permohonan."
                                , icon: "success"
                                , timer: 2000
                                , timerProgressBar: true
                                , allowOutsideClick: false
                                , showConfirmButton: false
                            }).then((result) => {
                                if (result.dismiss === Swal.DismissReason.timer) {
                                    location.reload()
                                }
                            });
                        } else {
                            Swal.fire({
                                title: "Gagal mengambil data!"
                                , icon: "error"
                            });
                        }
                    }
                });
            }
        });
    })

</script>
