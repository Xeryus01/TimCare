<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<style>
    :root {
        --brand: #2563eb;
        --brand-light: #eff6ff;
        --brand-dark: #1d4ed8;
        --danger: #dc2626;
        --danger-light: #fef2f2;
        --success: #16a34a;
        --text-primary: #0f172a;
        --text-secondary: #64748b;
        --text-muted: #94a3b8;
        --border: #e2e8f0;
        --input-bg: #f8fafc;
        --shadow-card: 0 1px 3px rgba(0,0,0,.07), 0 8px 24px rgba(0,0,0,.05);
    }
    
    .profile-page { padding: 36px 40px; }
    .page-header { margin-bottom: 32px; }
    .page-header h1 { font-size: 26px; font-weight: 700; color: var(--text-primary); }
    .page-header p { margin-top: 4px; font-size: 14px; color: var(--text-secondary); }
    
    .profile-grid { display: grid; grid-template-columns: 300px 1fr; gap: 24px; align-items: start; }
    
    .card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    
    .card-header { padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 12px; }
    .card-header-icon { width: 36px; height: 36px; border-radius: 8px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .card-header-icon.blue { background: var(--brand-light); color: var(--brand); }
    .card-header-icon.green { background: #f0fdf4; color: var(--success); }
    .card-header-icon.red { background: var(--danger-light); color: var(--danger); }
    .card-header h2 { font-size: 15px; font-weight: 600; }
    .card-header p { font-size: 13px; color: var(--text-secondary); margin-top: 1px; }
    .card-body { padding: 24px; }
    
    .profile-summary { display: flex; flex-direction: column; align-items: center; text-align: center; }
    .avatar-large { width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--brand), #7c3aed); color: #fff; font-size: 28px; font-weight: 700; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; box-shadow: 0 4px 16px rgba(37,99,235,.3); }
    .profile-name { font-size: 18px; font-weight: 700; }
    .profile-email { font-size: 13px; color: var(--text-secondary); margin-top: 4px; }
    .profile-badge { margin-top: 12px; display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 20px; background: var(--brand-light); color: var(--brand-dark); font-size: 12px; font-weight: 600; }
    
    .profile-meta { margin-top: 20px; width: 100%; border-top: 1px solid var(--border); padding-top: 20px; display: flex; flex-direction: column; gap: 12px; }
    .meta-item { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; }
    .meta-item svg { color: var(--text-muted); flex-shrink: 0; margin-top: 2px; }
    .meta-item strong { font-weight: 600; color: var(--text-primary); }
    .meta-item span { color: var(--text-secondary); font-size: 12px; }
    
    .left-stack { display: flex; flex-direction: column; gap: 24px; }
    .right-stack { display: flex; flex-direction: column; gap: 24px; }
    
    .form-group { margin-bottom: 20px; }
    .form-group:last-of-type { margin-bottom: 0; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
    
    label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    
    input[type="text"],
    input[type="email"],
    input[type="password"] {
        width: 100%;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1.5px solid var(--border);
        background: var(--input-bg);
        font-size: 14px;
        color: var(--text-primary);
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }
    
    input:focus { border-color: var(--brand); box-shadow: 0 0 0 4px rgba(37,99,235,.15); background: #fff; }
    
    .input-hint { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
    
    .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: opacity .15s, transform .1s; }
    .btn:active { transform: scale(.98); }
    .btn-primary { background: var(--brand); color: #fff; }
    .btn-primary:hover { opacity: .9; }
    .btn-danger { background: var(--danger); color: #fff; }
    .btn-danger:hover { opacity: .9; }
    
    .form-footer { display: flex; align-items: center; justify-content: space-between; padding-top: 8px; border-top: 1px solid var(--border); margin-top: 24px; font-size: 13px; }
    
    .danger-card { border-color: #fecaca; background: var(--danger-light); }
    .danger-card .card-header { border-bottom-color: #fecaca; }
    .danger-card .card-body p { font-size: 13px; color: #991b1b; line-height: 1.6; }
    
    .strength-bar { display: flex; gap: 4px; margin-top: 8px; }
    .strength-bar span { flex: 1; height: 3px; border-radius: 3px; background: var(--border); transition: background .3s; }
    
    .timeline { display: flex; flex-direction: column; gap: 0; }
    .timeline-item { display: flex; gap: 14px; padding: 12px 0; position: relative; }
    .timeline-item + .timeline-item::before { content: ''; position: absolute; top: 0; left: 17px; width: 1px; height: 12px; background: var(--border); }
    .timeline-dot { width: 10px; height: 10px; border-radius: 50%; border: 2px solid var(--brand); background: #fff; margin-top: 4px; flex-shrink: 0; margin-left: 13px; }
    .timeline-dot.green { border-color: var(--success); }
    .timeline-dot.orange { border-color: #f59e0b; }
    .timeline-content { flex: 1; }
    .timeline-content strong { font-size: 13px; font-weight: 600; display: block; }
    .timeline-content span { font-size: 12px; color: var(--text-muted); }
</style>

<div class="profile-page">
        <div class="page-header">
            <h1>Profile Settings</h1>
            <p>Kelola informasi akun dan preferensi Anda</p>
        </div>

        <div class="profile-grid">
            <!-- LEFT: Profile Summary & Activity -->
            <div class="left-stack">
                <!-- Profile Card -->
                <div class="card">
                    <div class="card-body profile-summary">
                        <div class="avatar-large"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></div>
                        <div class="profile-name"><?php echo e($user->name); ?></div>
                        <div class="profile-email"><?php echo e($user->email); ?></div>
                        <div class="profile-badge">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <?php echo e($user->roles->first()->name ?? 'User'); ?>

                        </div>

                        <div class="profile-meta">
                            <div class="meta-item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 01.13 2.18 2 2 0 012.11 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.09a16 16 0 006 6l.46-.46a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                                <div>
                                    <strong><?php echo e($user->phone_number ?? '-'); ?></strong>
                                    <span>Nomor HP</span>
                                </div>
                            </div>
                            <div class="meta-item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <div>
                                    <strong><?php echo e(optional($user->last_login_at)->format('d M Y') ?? '-'); ?></strong>
                                    <span>Login terakhir</span>
                                </div>
                            </div>
                            <div class="meta-item">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                <div>
                                    <strong>Aktif &amp; Terverifikasi</strong>
                                    <span>Status akun</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Activity Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                        </div>
                        <div>
                            <h2>Aktivitas Terkini</h2>
                            <p>Riwayat aksi akun</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-dot green"></div>
                                <div class="timeline-content">
                                    <strong>Login berhasil</strong>
                                    <span>Hari ini, 13:06</span>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div class="timeline-content">
                                    <strong>Password diperbarui</strong>
                                    <span>7 Jun 2026</span>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot orange"></div>
                                <div class="timeline-content">
                                    <strong>Profil diubah</strong>
                                    <span>25 Mei 2026</span>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-dot green"></div>
                                <div class="timeline-content">
                                    <strong>Akun dibuat</strong>
                                    <span>25 Mei 2026</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Edit Forms -->
            <div class="right-stack">
                <!-- Profile Info Card -->
                <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon blue">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
                        </div>
                        <div>
                            <h2>Informasi Profil</h2>
                            <p>Perbarui informasi pribadi Anda</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="send-verification" method="post" action="<?php echo e(route('verification.send')); ?>">
                            <?php echo csrf_field(); ?>
                        </form>

                        <form method="post" action="<?php echo e(route('profile.update')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('patch'); ?>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Nama Lengkap</label>
                                    <input type="text" id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required autocomplete="name">
                                    <?php if($errors->has('name')): ?>
                                        <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($errors->first('name')); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Nomor HP</label>
                                    <input type="text" id="phone" name="phone_number" value="<?php echo e(old('phone_number', $user->phone_number)); ?>" autocomplete="tel">
                                    <div class="input-hint">Format: +62xxxxxxxxxx</div>
                                    <?php if($errors->has('phone_number')): ?>
                                        <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($errors->first('phone_number')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="email">Alamat Email</label>
                                <input type="email" id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required autocomplete="email">
                                <div class="input-hint">Email verifikasi telah dikirim ke alamat ini</div>
                                <?php if($errors->has('email')): ?>
                                    <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($errors->first('email')); ?></div>
                                <?php endif; ?>
                            </div>

                            <?php if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail()): ?>
                                <div style="margin:20px 0; padding:12px; border-radius:8px; border:1px solid #fcd34d; background:#fef3c7;">
                                    <p style="font-size:12px;color:#92400e;">
                                        <?php echo e(__('Your email address is unverified.')); ?>

                                        <button form="send-verification" type="button" style="font-weight:600;text-decoration:underline;color:#92400e;background:none;border:none;cursor:pointer;">
                                            <?php echo e(__('Click here to re-send the verification email.')); ?>

                                        </button>
                                    </p>
                                    <?php if(session('status') === 'verification-link-sent'): ?>
                                        <p style="margin-top:8px;font-size:12px;color:#15803d;"><?php echo e(__('A new verification link has been sent to your email address.')); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <div class="form-footer">
                                <span style="color:var(--text-muted);">Terakhir diperbarui: <?php echo e($user->updated_at->format('d M Y')); ?></span>
                                <button type="submit" class="btn btn-primary">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                                    Simpan Perubahan
                                </button>
                            </div>

                            <?php if(session('status') === 'profile-updated'): ?>
                                <div style="margin-top:16px; padding:12px; border-radius:8px; border:1px solid #86efac; background:#f0fdf4;">
                                    <p style="font-size:12px;color:#15803d;font-weight:600;">✓ Profil berhasil diperbarui</p>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>

                <!-- Password Card -->
                <!-- <div class="card">
                    <div class="card-header">
                        <div class="card-header-icon green">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                        </div>
                        <div>
                            <h2>Ubah Password</h2>
                            <p>Pastikan akun Anda menggunakan password yang kuat</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo e(route('password.update')); ?>">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('put'); ?>

                            <div class="form-group">
                                <label for="current_password">Password Saat Ini</label>
                                <input type="password" id="current_password" name="current_password" placeholder="Masukkan password lama" autocomplete="current-password">
                                <?php if($errors->updatePassword->has('current_password')): ?>
                                    <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($errors->updatePassword->first('current_password')); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="new_password">Password Baru</label>
                                    <input type="password" id="new_password" name="password" placeholder="Min. 8 karakter" autocomplete="new-password" oninput="checkStrength(this.value)">
                                    <div class="strength-bar">
                                        <span id="s1"></span><span id="s2"></span><span id="s3"></span><span id="s4"></span>
                                    </div>
                                    <div class="input-hint" id="strength-label">Masukkan password baru</div>
                                    <?php if($errors->updatePassword->has('password')): ?>
                                        <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($errors->updatePassword->first('password')); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Konfirmasi Password</label>
                                    <input type="password" id="confirm_password" name="password_confirmation" placeholder="Ulangi password baru" autocomplete="new-password">
                                    <?php if($errors->updatePassword->has('password_confirmation')): ?>
                                        <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($errors->updatePassword->first('password_confirmation')); ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <p style="font-size:12px;color:var(--text-muted);margin:20px 0;">Gunakan minimal 8 karakter dengan kombinasi huruf &amp; angka</p>

                            <div class="form-footer">
                                <span style="color:var(--text-muted);">Keamanan berlapis</span>
                                <button type="submit" class="btn btn-primary">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                    Simpan Password
                                </button>
                            </div>

                            <?php if(session('status') === 'password-updated'): ?>
                                <div style="margin-top:16px; padding:12px; border-radius:8px; border:1px solid #86efac; background:#f0fdf4;">
                                    <p style="font-size:12px;color:#15803d;font-weight:600;">✓ Password berhasil diperbarui</p>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div> -->

                <!-- Danger Zone -->
                <!-- <div class="card danger-card">
                    <div class="card-header" style="border-bottom-color:#fecaca;">
                        <div class="card-header-icon red">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </div>
                        <div>
                            <h2 style="color:var(--danger);">Hapus Akun</h2>
                            <p style="color:#991b1b;">Tindakan ini tidak dapat dibatalkan</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <p style="font-size:14px;color:#991b1b;line-height:1.7;margin-bottom:20px;">Setelah akun dihapus, semua data dan sumber daya terkait akan dihapus secara permanen. Pastikan Anda yakin sebelum melanjutkan.</p>
                        <form method="post" action="<?php echo e(route('profile.destroy')); ?>" onsubmit="return confirm('Apakah Anda yakin? Tindakan ini tidak dapat diurungkan.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('delete'); ?>

                            <div class="form-group" style="margin-bottom:16px;">
                                <label for="password_delete">Konfirmasi Password</label>
                                <input type="password" id="password_delete" name="password" required placeholder="••••••••">
                                <?php if($errors->userDeletion->has('password')): ?>
                                    <div style="font-size:12px;color:#dc2626;margin-top:4px;"><?php echo e($errors->userDeletion->first('password')); ?></div>
                                <?php endif; ?>
                            </div>

                            <div style="display:flex;gap:12px;align-items:center;">
                                <button type="submit" class="btn btn-danger">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m5 0V4a1 1 0 011-1h2a1 1 0 011 1v2"/></svg>
                                    Hapus Akun Saya
                                </button>
                                <span style="font-size:12px;color:#b91c1c;">Masukkan password untuk konfirmasi</span>
                            </div>
                        </form>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</div>

<script>
function checkStrength(val) {
    const bars = [document.getElementById('s1'), document.getElementById('s2'), document.getElementById('s3'), document.getElementById('s4')];
    const label = document.getElementById('strength-label');
    bars.forEach(b => b.style.background = 'var(--border)');

    if (!val) { label.textContent = 'Masukkan password baru'; label.style.color = 'var(--text-muted)'; return; }

    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const colors = ['#ef4444', '#f59e0b', '#3b82f6', '#16a34a'];
    const labels = ['Terlalu lemah', 'Lemah', 'Cukup kuat', 'Sangat kuat'];
    const textColors = ['#dc2626', '#b45309', '#1d4ed8', '#15803d'];

    for (let i = 0; i < score; i++) bars[i].style.background = colors[score - 1];
    label.textContent = labels[score - 1];
    label.style.color = textColors[score - 1];
}
</script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\projects\timcare - Copy\resources\views/profile/edit.blade.php ENDPATH**/ ?>