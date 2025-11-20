<div class="container-fluid py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0">Formulir Data Pengantin</h5>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-primary mb-4">
                <i class="bi bi-info-circle-fill me-2"></i>Silahkan isi form berikut untuk melengkapi data pengantin, pastikan semua data yang diinput sudah benar.
            </div>

            <div class="form-floating mb-4">
                <select id="kua" class="form-select" required>
                    <option value="0">==PILIHAN==</option>
                    <option value="2">TAPIN UTARA</option>
                    <option value="3">TAPIN TENGAH</option>
                    <option value="4">TAPIN SELATAN</option>
                    <option value="5">BAKARANGAN</option>
                    <option value="6">BUNGUR</option>
                    <option value="7">LOKPAIKAT</option>
                    <option value="8">PIANI</option>
                    <option value="9">SALAM BABARIS</option>
                    <option value="10">HATUNGUN</option>
                    <option value="11">BINUANG</option>
                    <option value="12">CANDI LARAS SELATAN</option>
                    <option value="13">CANDI LARAS UTARA</option>
                </select>
                <label for="kua"><i class="bi bi-geo-alt-fill text-danger me-2"></i>Kecamatan KUA</label>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-person-fill me-2"></i>Data Calon Suami</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-floating mb-3">
                                <input type="text" id="lk-kk" class="form-control" placeholder="Nomor Kartu Keluarga" aria-label="Nomor Kartu Keluarga" required>
                                <label for="lk-kk">Nomor Kartu Keluarga</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" id="lk-nik" class="form-control" placeholder="NIK (Nomor Induk Kependudukan)" aria-label="NIK (Nomor Induk Kependudukan)" required>
                                <label for="lk-nik">NIK (Nomor Induk Kependudukan)</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" id="lk-name" class="form-control" placeholder="Nama Lengkap" aria-label="Nama Lengkap" required>
                                <label for="lk-name">Nama Lengkap</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" id="lk-phone" class="form-control" placeholder="Nomor Telepon (WhatsApp)" aria-label="Nomor Telepon (WhatsApp)" required>
                                <label for="lk-phone">Nomor Telepon (WhatsApp)</label>
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Alamat Tinggal" id="lk-addr" style="height: 120px;" required></textarea>
                                <label for="lk-addr">Alamat Tinggal (Sesuai Kartu Keluarga)</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold"><i class="bi bi-person-fill me-2"></i>Data Calon Istri</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-floating mb-3">
                                <input type="text" id="pr-kk" class="form-control" placeholder="Nomor Kartu Keluarga" aria-label="Nomor Kartu Keluarga" required>
                                <label for="pr-kk">Nomor Kartu Keluarga</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" id="pr-nik" class="form-control" placeholder="NIK (Nomor Induk Kependudukan)" aria-label="NIK (Nomor Induk Kependudukan)" required>
                                <label for="pr-nik">NIK (Nomor Induk Kependudukan)</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" id="pr-name" class="form-control" placeholder="Nama Lengkap" aria-label="Nama Lengkap" required>
                                <label for="pr-name">Nama Lengkap</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" id="pr-phone" class="form-control" placeholder="Nomor Telepon (WhatsApp)" aria-label="Nomor Telepon (WhatsApp)" required>
                                <label for="pr-phone">Nomor Telepon (WhatsApp)</label>
                            </div>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="Alamat Tinggal" id="pr-addr" style="height: 120px;" required></textarea>
                                <label for="pr-addr">Alamat Tinggal (Sesuai Kartu Keluarga)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0"><i class="bi bi-house-door-fill me-2"></i>Alamat Baru (Setelah Menikah)</h5>
        </div>
        <div class="card-body p-4">
            <div class="form-floating mb-3">
                <input type="text" autocomplete="off" class="form-control" name="addr-street" id="addr-street" placeholder="Nama Jalan" required>
                <label for="addr-street">Nama Jalan</label>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" autocomplete="off" class="form-control" name="addr-rt" id="addr-rt" placeholder="RT" maxlength="3" required>
                        <label for="addr-rt">RT</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" autocomplete="off" class="form-control" name="addr-rw" id="addr-rw" placeholder="RW" maxlength="3" required>
                        <label for="addr-rw">RW</label>
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" autocomplete="off" class="form-control" name="addr-ds" id="addr-ds" placeholder="Desa" required>
                        <label for="addr-ds">Kelurahan/Desa</label>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-floating">
                        <input type="text" autocomplete="off" class="form-control" name="addr-kec" id="addr-kec" placeholder="Kecamatan" required>
                        <label for="addr-kec">Kecamatan</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light py-3">
            <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Catatan/Perubahan Data</h5>
        </div>
        <div class="card-body p-4">
            <div class="form-floating mb-3">
                <textarea class="form-control" placeholder="CATATAN" style="height: 150px;" name="notes" id="notes" required></textarea>
                <label for="notes">Pendidikan/Pekerjaan/Golongan Darah atau catatan lainnya</label>
            </div>
            <div class="alert alert-warning">
                <small><i class="bi bi-exclamation-triangle-fill me-2"></i>Untuk perubahan data seperti pendidikan, lampirkan <strong>ijazah terakhir</strong>. Untuk perubahan pekerjaan, lampirkan <strong>SK Kerja</strong>.</small>
            </div>
            <div class="mt-4 d-grid">
                <input id="csrf-token" type="hidden" name="csrf_token" value="{{ $csrf_token }}">
                <button id="send-from" class="btn btn-success btn-lg">Kirim Permohonan</button>
            </div>
        </div>
    </div>
</div>
<div id="progress-bar" class="progress fixed-bottom" style="height: 5px; border-radius: 0;">
    <div class="progress-bar bg-success progress-bar-striped progress-bar-animated" style="width: 0%" id="progress-bar-fill"></div>
</div>

<script>
    $(document).ready(function() {
        $("button").click(function(e) {
            e.preventDefault();

            var isValid = true;

            $("input[required], textarea[required], select[required]").each(function() {
                if ($(this).val() == "" || $(this).val() == "0") {
                    isValid = false;
                    $(this).addClass("is-invalid");
                    $(this).siblings(".invalid-feedback").remove();
                    $(this).after('<div class="invalid-feedback">Wajib disi.</div>');
                } else {
                    $(this).removeClass("is-invalid");
                    $(this).siblings(".invalid-feedback").remove();
                }
            });

            if (!isValid) {
                return;
            }

            var formData = {
                kua: $("#kua").val()
                , lk_kk: $("#lk-kk").val()
                , lk_nik: $("#lk-nik").val()
                , lk_name: $("#lk-name").val()
                , lk_phone: $("#lk-phone").val()
                , lk_addr: $("#lk-addr").val()
                , pr_kk: $("#pr-kk").val()
                , pr_nik: $("#pr-nik").val()
                , pr_name: $("#pr-name").val()
                , pr_phone: $("#pr-phone").val()
                , pr_addr: $("#pr-addr").val()
                , addr_street: $("#addr-street").val()
                , addr_rt: $("#addr-rt").val()
                , addr_rw: $("#addr-rw").val()
                , addr_ds: $("#addr-ds").val()
                , addr_kec: $("#addr-kec").val()
                , notes: $("#notes").val()
                , csrf_token: $('#csrf-token').val()
            };

            $.ajax({
                type: "POST"
                , url: "/product/register"
                , data: formData
                , success: function(result) {
                    if (result.status === 'success') {
                        Swal.fire({
                            title: 'Sukses'
                            , text: result.message
                            , icon: 'success'
                            , timer: 3000
                            , timerProgressBar: true
                            , showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', result.message, 'error');
                    }
                }
                , error: function(xhr, status, error) {
                    console.error("Terjadi kesalahan: " + error);
                }
            });
        });
    });


    $(window).on('scroll', function() {
        var scrollTop = $(window).scrollTop();
        var docHeight = $(document).height();
        var winHeight = $(window).height();

        var scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;

        $('#progress-bar-fill').css('width', scrollPercent + '%');
    });

</script>
