<div class="bg-light p-md-4 min-vh-100">
    <div class="container py-4">
        <div class="alert alert-info text-center small mb-4">Silahkan unggah dokumen pendukung untuk <strong>perubahan
                data</strong>. Seperti ijazah, akta kelahiran dan lain sebagainya.</div>

        <!-- Upload Form Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Unggah Dokumen Pendukung</h5>
            </div>
            <div class="card-body">
                <form id="uploadForm">
                    <div class="mb-3">
                        <label for="file-category" class="form-label">Pilih kategori dokumen.</label>
                        <select class="form-select form-select-lg" id="file-category" name="category"
                            aria-label="Kategori">
                            <option selected>--Jenis Dokumen--</option>
                            <option value="1">Ijazah Terakhir</option>
                            <option value="2">Akta Kelahiran</option>
                            <option value="3">SK Kerja</option>
                            <option value="4">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="file" class="form-label">
                            <span class="d-block">
                                Pilih file untuk diunggah.
                            </span>
                            <span class="required small">Format file berupa gambar atau dokumen pdf (tidak lebih dari
                                1MB)</span>
                        </label>
                        <input class="form-control form-control-lg" type="file" id="file" name="file"
                            accept=".jpg, .jpeg, .png, .gif, .pdf">
                        <div class="progress mt-3" style="display:none;">
                            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="mt-4 d-grid">
                        <input id="csrf-token" type="hidden" name="csrf_token" value="{{ $csrf_token }}">
                        <button type="submit" class="btn btn-primary btn-lg">Unggah File</button>
                    </div>
                </form>
            </div>
        </div>

        @if ($user_upload_files && count($user_upload_files) > 0)
        <!-- Uploaded Files Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Dokumen Terunggah</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Jenis Dokumen</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($user_upload_files as $item)
                            <tr class="align-middle">
                                <td class="p-3">
                                    @if ($item['file_category'] == 1)
                                    Ijazah Terakhir
                                    @elseif ($item['file_category'] == 2)
                                    Akta Kelahiran
                                    @elseif ($item['file_category'] == 3)
                                    SK Kerja
                                    @elseif ($item['file_category'] == 4)
                                    Lainnya
                                    @endif
                                    <div class="small text-muted">{{ $item['file_created'] }}</div>
                                </td>
                                <td class="p-3 text-center">
                                    <button type="button" class="btn btn-primary btn-sm file-show me-1"
                                        data-path="{{ $item['file_path'] }}">Lihat</button>
                                    <button type="button" class="btn btn-danger btn-sm file-delete"
                                        data-pid="{{ $item['id'] }}" data-path="{{ $item['file_path'] }}">Hapus</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Completion Card -->
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <p class="mb-3 lead">Konfirmasi data yang sudah diinput, dengan menekan tombol selesai dibawah ini.</p>
                <div class="d-grid">
                    <button type="button" id="form-complete" class="btn btn-success btn-lg">Selesai</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function blobFile(path) {
        $.ajax({
            url: '/file/get',
            type: 'POST',
            data: { path },
            xhrFields: {
                responseType: 'blob'
            },
            success: function (blob) {
                const url = URL.createObjectURL(blob);
                const newTab = window.open();

                if (newTab) {
                    newTab.location.href = url;
                } else {
                    Swal.fire('Error', 'Popup diblokir! Izinkan pop-up untuk melihat file.', 'error');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Gagal memuat file: ' + xhr.status + ' ' + xhr.statusText, 'error');
            }
        });
    }

    $(document).ready(function () {
        $('#uploadForm').on('submit', function (e) {
            e.preventDefault();

            const fileInput = $('#file')[0];
            const category = $('#file-category').val();

            if (!fileInput.files.length) {
                Swal.fire('Error', 'Pilih file terlebih dahulu', 'error');
                return;
            }
            if (category === '--Jenis Dokumen--') {
                Swal.fire('Error', 'Pilih kategori dokumen', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('category', category);
            formData.append('csrf_token', $('#csrf-token').val());

            const progress = $('.progress');
            const progressBar = $('.progress-bar');

            $.ajax({
                url: '/file/upload',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function () {
                    const xhr = new XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function (e) {
                        if (e.lengthComputable) {
                            const percent = (e.loaded / e.total) * 100;
                            progressBar.css('width', percent + '%');
                            progressBar.attr('aria-valuenow', percent);
                            console.log('Upload progress:', percent);
                        }
                    });
                    return xhr;
                },
                success: function (response) {
                    progress.hide();
                    try {
                        const result = typeof response === 'string' ? JSON.parse(response) : response;
                        if (result.status === 'success') {
                            Swal.fire({
                                title: 'Sukses',
                                text: result.message,
                                icon: 'success',
                                timer: 3000, 
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                $('#uploadForm')[0].reset();
                                location.reload();
                            });

                            const uploadedFilePath = result.filePath || result.message.match(/file_.*\.(jpg|jpeg|png|gif|pdf)/)?.[0];

                            if (uploadedFilePath) {
                                console.log('Uploaded file path:', uploadedFilePath);
                            }
                        } else {
                            Swal.fire('Error', result.message, 'error');
                        }
                    } catch (err) {
                        Swal.fire('Error', 'Gagal memproses response dari server', 'error');
                    }
                },
                error: function (xhr, status, error) {
                    progress.hide();
                    Swal.fire('Error', 'Terjadi kesalahan saat upload: ' + error, 'error');
                },
                beforeSend: function () {
                    progress.show();
                    progressBar.css('width', '0%');
                }
            });
        });

        $(".file-show").click(function () {
            const filePath = $(this).data('path');
            blobFile(filePath);
        })

        $(".file-delete").click(function () {
            const fileId = $(this).data('pid');
            const filePath = $(this).data('path');
            const csrfToken = $('#csrf-token').val();

            if (!fileId || !filePath || !csrfToken) {
                console.error('Missing required data for file deletion');
                return;
            }

            Swal.fire({
                title: 'Konfirmasi',
                text: 'Apakah Anda yakin ingin menghapus dokumen ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/file/delete',
                        type: 'POST',
                        data: {
                            file_id: fileId,
                            file_path: filePath,
                            csrf_token: csrfToken
                        },
                        success: function (response) {
                            Swal.fire({
                                title: response.status === 'success' ? 'Sukses' : 'Error',
                                text: response.message,
                                icon: response.status,
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function (xhr, status, error) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Terjadi kesalahan saat menghapus file: ' + error,
                                icon: 'error',
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: false
                            });
                            console.error('Delete error:', error);
                        }
                    });
                }
            });
        });

        $("#form-complete").click(function () {
            Swal.fire({
                title: 'Konfirmasi',
                text: 'Akhiri pengisian formulir registrasi?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Selesai',
                cancelButtonText: 'Batal',
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/product/complete',
                        type: 'POST',
                        success: function (response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    title: 'Sukses',
                                    text: response.message,
                                    icon: 'success',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload()
                                })
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: response.message,
                                    icon: 'error',
                                    timer: 3000,
                                    timerProgressBar: true,
                                    showConfirmButton: false
                                })
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error(error)
                        }
                    });
                }
            });
        });

    });
</script>