<?php
session_start();

// 🔒 1. SECURITY GATEKEEPER
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'Student') {
    header("Location: ../student_login.php"); exit();
}

include '../includes/db_connect.php';
include '../includes/header.php';
include '../includes/student_sidebar.php';

$user_id = $_SESSION['user_id'];

try {
    // Fetch latest user data
    $stmt = $pdo->prepare("SELECT * FROM Users WHERE UserID = :uid");
    $stmt->execute(['uid' => $user_id]);
    $user = $stmt->fetch();
} catch (PDOException $e) { 
    die("Profile Fetch Error: " . $e->getMessage()); 
}

// Profile Picture logic: Use DB path or dynamic avatar if empty
$profile_pic = !empty($user['ProfilePicture']) ? $user['ProfilePicture'] : 'https://ui-avatars.com/api/?name=' . urlencode($user['FirstName'] . ' ' . $user['LastName']) . '&background=059669&color=fff&size=256';
?>

<main class="flex-1 p-5 lg:p-12 lg:ml-72 bg-slate-50/50 min-h-screen transition-all duration-500">
    <header class="mb-12">
        <h2 class="text-4xl font-black text-slate-900 tracking-tight">My Profile</h2>
        <p class="text-slate-500 font-medium mt-1">Personal Information & Security Settings</p>
    </header>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-8 py-5 rounded-[2rem] mb-10 font-black text-[14px] uppercase tracking-widest flex items-center gap-4 shadow-sm">
            <span class="text-lg">✅</span> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12 items-start">
        
        <div class="lg:col-span-1">
            <div class="bg-slate-900 rounded-[3.5rem] p-12 text-center shadow-2xl relative overflow-hidden group border border-white/5">
                <div class="absolute inset-0 bg-gradient-to-b from-emerald-600/20 to-transparent pointer-events-none"></div>
                
                <div class="relative z-10">
                    <div class="relative w-44 h-44 mx-auto mb-10">
                        <div class="w-full h-full rounded-full border-4 border-white/20 shadow-2xl overflow-hidden bg-white ring-8 ring-white/5">
                            <img id="previewImage" src="<?= htmlspecialchars($profile_pic) ?>" alt="Profile" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <label for="profileUpload" class="absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-all duration-300 cursor-pointer backdrop-blur-sm">
                                <span class="text-[9px] font-black uppercase tracking-widest mb-1">Update</span>
                                <span class="text-[12px]">📸</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="button" onclick="openProfileModal('<?= htmlspecialchars($profile_pic) ?>')" class="mb-10 bg-white/10 hover:bg-white/20 text-white border border-white/10 px-8 py-3.5 rounded-2xl text-[9px] font-black uppercase tracking-[0.2em] transition-all active:scale-95 shadow-lg">
                        View Full Photo 👁️
                    </button>
                    
                    <h3 class="text-3xl font-black text-white leading-tight uppercase tracking-tight"><?= htmlspecialchars($user['FirstName'] . ' ' . $user['LastName']) ?></h3>
                    <p class="text-emerald-400 font-black mt-2 uppercase text-[14px] tracking-[0.3em]"><?= htmlspecialchars($user['Major'] ?? 'ScholarLink Student') ?></p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2">
            <form action="../actions/process_profile.php" method="POST" enctype="multipart/form-data" id="profileForm" class="bg-white rounded-[3.5rem] border border-slate-200/60 shadow-sm p-12 space-y-12">
                <input type="file" id="profileUpload" name="profile_picture" accept="image/jpeg, image/png, image/webp" class="hidden">
                
                <div>
                    <div class="flex items-center gap-4 mb-10">
                        <div class="h-10 w-1.5 bg-slate-900 rounded-full"></div>
                        <h4 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Account Access</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="block text-[14px] font-black text-slate-900 uppercase tracking-widest ml-1">Student ID (Read-Only)</label>
                            <input type="text" value="<?= htmlspecialchars($user['StudentID_Num']) ?>" disabled class="w-full bg-slate-50 border border-slate-100 px-7 py-5 rounded-[1.5rem] font-bold text-slate-900 cursor-not-allowed text-sm">
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[14px] font-black text-slate-900 uppercase tracking-widest ml-1">Email Address</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($user['Email']) ?>" class="w-full bg-white border border-slate-200 px-7 py-5 rounded-[1.5rem] font-bold text-slate-900 outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm">
                        </div>
                    </div>
                </div>

                <div class="h-px bg-slate-100"></div>

                <div>
                    <div class="flex items-center gap-4 mb-10">
                        <div class="h-10 w-1.5 bg-emerald-600 rounded-full"></div>
                        <h4 class="text-2xl font-black text-slate-900 tracking-tight uppercase">Personal Details</h4>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div class="space-y-4">
                            <label class="block text-[14px] font-black text-slate-900 uppercase tracking-widest ml-1">Contact Number</label>
                            <input type="text" name="contact_number" id="phone_mask" value="<?= htmlspecialchars($user['ContactNumber'] ?? '') ?>" class="w-full bg-white border border-slate-200 px-7 py-5 rounded-[1.5rem] font-bold text-slate-900 outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm" placeholder="09XX-XXX-XXXX">
                        </div>
                        <div class="space-y-4">
                            <label class="block text-[14px] font-black text-slate-900 uppercase tracking-widest ml-1">Birth Date</label>
                            <input type="date" name="date_of_birth" value="<?= htmlspecialchars($user['DateOfBirth'] ?? '') ?>" class="w-full bg-white border border-slate-200 px-7 py-5 rounded-[1.5rem] font-bold text-slate-900 outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full bg-slate-900 text-white py-7 rounded-[2rem] font-black text-[11px] uppercase tracking-[0.3em] hover:bg-emerald-600 shadow-2xl transition-all hover:-translate-y-1 active:scale-95">
                    Save Profile Changes 💾
                </button>
            </form>
        </div>
    </div>
</main>

<div id="fullPhotoModal" class="fixed inset-0 bg-slate-900/90 backdrop-blur-md z-[9999] hidden items-center justify-center p-8">
    <div class="bg-white rounded-[4rem] shadow-2xl overflow-hidden max-w-xl w-full p-3 animate-in fade-in zoom-in duration-300">
        <div class="relative">
            <img id="modalFullImage" src="" class="w-full aspect-square object-cover rounded-[3.5rem] shadow-inner">
            <button onclick="closeProfileModal()" class="absolute top-8 right-8 w-14 h-14 bg-white/90 backdrop-blur-sm rounded-[1.5rem] flex items-center justify-center text-slate-900 font-bold text-2xl shadow-2xl hover:bg-red-500 hover:text-white transition-all transform hover:rotate-90">
                &times;
            </button>
        </div>
    </div>
</div>

<script>
    // 1. MODAL LOGIC
    function openProfileModal(imgSrc) {
        const modal = document.getElementById('fullPhotoModal');
        const modalImg = document.getElementById('modalFullImage');
        modalImg.src = imgSrc;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeProfileModal() {
        const modal = document.getElementById('fullPhotoModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }

    window.addEventListener('click', function(e) {
        if (e.target.id === 'fullPhotoModal') closeProfileModal();
    });

    // 2. LIVE IMAGE PREVIEW
    document.addEventListener("DOMContentLoaded", function() {
        const profileUpload = document.getElementById('profileUpload');
        if (profileUpload) {
            profileUpload.addEventListener('change', function() {
                const preview = document.getElementById('previewImage');
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onloadend = () => { 
                        preview.src = reader.result;
                        document.getElementById('modalFullImage').src = reader.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // 3. ADVANCED PHONE NUMBER MASKING
        const phoneInput = document.getElementById('phone_mask');
        if (phoneInput) {
            let realPhone = phoneInput.value;

            function formatDashes(val) {
                let v = val.replace(/\D/g, '').substring(0, 11);
                if (v.length > 7) v = v.substring(0, 4) + '-' + v.substring(4, 7) + '-' + v.substring(7);
                else if (v.length > 4) v = v.substring(0, 4) + '-' + v.substring(4);
                return v;
            }

            function hideDigits(val) {
                if (val.length < 9) return val;
                return val.substring(0, 5) + '***-****';
            }

            // Initialization
            phoneInput.value = hideDigits(formatDashes(realPhone));

            phoneInput.addEventListener('focus', function() {
                this.value = realPhone;
            });

            phoneInput.addEventListener('blur', function() {
                realPhone = formatDashes(this.value);
                this.value = hideDigits(realPhone);
            });

            phoneInput.addEventListener('input', function() {
                this.value = formatDashes(this.value);
                realPhone = this.value;
            });

            phoneInput.addEventListener('keypress', function(e) {
                if (!/[0-9]/.test(e.key)) e.preventDefault();
            });

            // Clean submission
            const form = document.getElementById('profileForm');
            if (form) {
                form.addEventListener('submit', function() {
                    phoneInput.value = realPhone.replace(/-/g, ''); 
                });
            }
        }
    });
</script>
<?php include '../includes/footer.php'; ?>