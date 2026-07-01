<?php
session_start();
// If already logged in, redirect to dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: student/dashboard.php");
    exit;
}

// Fetch programs for the registration dropdown
require 'includes/db_connect.php';
try {
    $stmt = $pdo->query("SELECT ProgramID as program_id, ProgramName as program_name FROM program");
    $programs = $stmt->fetchAll();
} catch (PDOException $e) {
    $programs = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - ScholarLink</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .modal-active { display: flex !important; animation: fadeIn 0.2s ease-out forwards; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 md:p-10 relative">

    <div class="bg-white w-full max-w-7xl rounded-[1.5rem] md:rounded-[2.5rem] shadow-2xl shadow-slate-200/50 border border-slate-100 flex overflow-hidden min-h-[85vh] md:min-h-[80vh]">
        
        <div class="w-1/2 bg-slate-900 p-16 flex flex-col justify-between relative overflow-hidden hidden md:flex">
            <img src="https://images.unsplash.com/photo-1541339907198-e08756defefe?q=80&w=1470" class="absolute inset-0 w-full h-full object-cover opacity-20">
            <div class="absolute inset-0 bg-gradient-to-b from-green-600/50 to-slate-900/90 z-10"></div>
            <div class="relative z-20">
                <img src="assets/img/tau_logo.png" alt="TAU Logo" class="h-28 w-auto mb-10">
                <div class="space-y-3">
                    <span class="inline-block px-4 py-1 rounded-full bg-green-500/20 text-green-300 text-xs font-bold uppercase tracking-widest">Student Portal</span>
                    <h1 class="text-6xl font-black text-white leading-tight tracking-tighter">Welcome<br>Back.</h1>
                    <p class="text-green-100 text-lg max-w-md font-medium">Access your ScholarLink account to manage applications, view requirements, and check scholarship status.</p>
                </div>
            </div>
            <div class="relative z-20 flex items-center justify-between border-t border-white/10 pt-8 mt-16">
                <p class="text-sm font-medium text-white/70">Tarlac Agricultural University Scholarship Management System</p>
                <p class="text-[10px] font-black text-white/50 uppercase tracking-widest">SCHOLARLINK | TAU | CAMPUS</p>
            </div>
        </div>

        <div class="w-full md:w-1/2 p-6 sm:p-10 md:p-20 flex flex-col justify-center relative">
            <div class="md:hidden mb-8 text-center">
                <img src="assets/img/tau_logo.png" alt="TAU Logo" class="h-20 w-auto mx-auto">
            </div>

            <div class="max-w-md mx-auto w-full">
                <div class="mb-10 md:mb-12 text-center md:text-left">
                    <h2 class="text-xs font-black text-green-600 uppercase tracking-widest mb-2">Secure Access</h2>
                    <h3 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Login</h3>
                    <p class="text-slate-500 text-sm md:text-base font-medium mt-2">Enter your credentials below to access the portal.</p>
                </div>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-2xl mb-6 text-sm font-bold flex items-center gap-3">
                        ⚠️ <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>
                <?php if(isset($_SESSION['success'])): ?>
                    <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-2xl mb-6 text-sm font-bold flex items-center gap-3">
                        ✅ <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <form action="actions/process_login.php" method="POST" id="loginForm" class="space-y-5 md:space-y-6">
                    <input type="hidden" name="login_type" value="student">
                    
                    <div>
                        <label class="block text-xs md:text-sm font-semibold text-slate-800 mb-2">Student Number</label>
                        <input type="text" name="username" id="login_student_number" required placeholder="e.g. 2023100194" maxlength="10" pattern="[0-9]{10}"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                            class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-white text-slate-900 font-bold placeholder:text-slate-400 focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all outline-none">
                        <p class="text-[10px] text-slate-400 font-medium mt-1">Must be exactly a 10-digit numerical entry.</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs md:text-sm font-semibold text-slate-800">Password</label>
                            <button type="button" onclick="openForgotModal()" class="text-xs md:text-sm font-semibold text-green-600 hover:text-green-700">Forgot?</button>
                        </div>
                        
                        <div class="relative">
                            <input type="password" name="password" id="login_password" required placeholder="••••••••" 
                                class="w-full pl-5 pr-12 py-4 rounded-2xl border border-slate-200 bg-white text-slate-900 font-medium placeholder:text-slate-400 focus:border-green-400 focus:ring-4 focus:ring-green-100 transition-all outline-none">
                            <button type="button" onclick="togglePassword('login_password', 'eye_icon_login')" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-green-600 focus:outline-none">
                                <svg id="eye_icon_login" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-6 rounded-2xl transition-all shadow-lg shadow-slate-900/20 active:scale-[0.98]">
                        Sign In to Portal
                    </button>
                </form>

                <div class="mt-10 md:mt-12 pt-8 border-t border-slate-100 text-center space-y-4">
                    <p class="text-sm md:text-base text-slate-600 font-medium">New Student Applicant? <br class="sm:hidden">
                        <button onclick="openRegisterModal()" type="button" class="font-bold text-green-600 hover:text-green-700 mt-2 sm:mt-0 transition-colors">Create an Account →</button>
                    </p>
                    
                    <p class="text-sm md:text-base text-slate-600 font-medium">Are you an Administrator? <br class="sm:hidden">
                        <a href="admin_login.php" class="font-bold text-green-600 hover:text-green-700 mt-2 sm:mt-0 transition-colors">Go to Staff Portal →</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- REGISTRATION MODAL -->
    <div id="registerModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-[95%] sm:max-w-2xl rounded-[1.5rem] md:rounded-[2rem] shadow-2xl relative max-h-[90vh] overflow-y-auto">
            <button onclick="closeRegisterModal()" class="absolute top-4 right-4 md:top-6 md:right-6 w-8 h-8 md:w-10 md:h-10 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center hover:bg-slate-200 hover:text-slate-900 transition-colors z-10">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="p-6 md:p-10">
                <div class="mb-6 md:mb-8 pr-8">
                    <h3 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">Register Account</h3>
                    <p class="text-slate-500 text-sm md:text-base font-medium mt-1">Create your ScholarLink profile to apply for programs.</p>
                </div>

                <div class="mb-6 p-5 border-2 border-dashed border-green-300 bg-green-50/50 rounded-2xl text-center relative transition-all duration-300" id="scanArea">
                    <label class="block text-xs font-bold text-green-800 mb-3 uppercase tracking-widest" id="scanAreaLabel">📸 Auto-Fill via ID Scan (Front & Back)</label>
                    
                    <div id="previewContainer" class="hidden flex justify-center gap-4 mb-4">
                        <div class="relative w-24 h-16 bg-slate-200 rounded-lg overflow-hidden border-2 border-green-400 shadow-sm">
                            <img id="frontPreview" class="w-full h-full object-cover hidden">
                            <div id="frontPreviewText" class="absolute inset-0 flex items-center justify-center text-slate-400 text-[9px] font-black uppercase">Front</div>
                        </div>
                        <div class="relative w-24 h-16 bg-slate-200 rounded-lg overflow-hidden border-2 border-dashed border-slate-400 opacity-70" id="backPreviewBox">
                            <img id="backPreview" class="w-full h-full object-cover hidden">
                            <div id="backPreviewText" class="absolute inset-0 flex items-center justify-center text-slate-400 text-[9px] font-black uppercase">Back</div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 justify-center items-center" id="scanMethodToggle">
                        
                        <input type="file" id="fallbackCameraInput" accept="image/*" capture="environment" class="hidden">

                        <button type="button" onclick="startCamera()" id="btnStartCamera" class="w-full sm:w-auto bg-green-600 text-white px-6 py-2.5 rounded-full text-[11px] font-black uppercase tracking-widest shadow-md hover:bg-green-700 transition-all hover:scale-105 active:scale-95 flex items-center justify-center gap-2">
                            <span>📷</span> <span id="cameraBtnText">Take Photo Live</span>
                        </button>
                        <span class="text-xs font-bold text-green-700/50" id="orDivider">OR</span>
                        <div class="relative w-full sm:w-auto" id="uploadWrapper">
                            <input type="file" id="idUploader" accept="image/*" multiple class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="bg-white border border-green-300 text-green-700 px-6 py-2.5 rounded-full text-[11px] font-black uppercase tracking-widest hover:bg-green-50 transition-all flex items-center justify-center gap-2">
                                <span>📁</span> <span id="uploadBtnText">Upload Files</span>
                            </div>
                        </div>
                    </div>

                    <div id="cameraContainer" class="hidden flex-col items-center gap-4 mt-2 relative">
                        <div class="absolute -top-3 z-30 bg-green-600 text-white px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-md" id="camInstructionBadge">
                            Position FRONT of ID
                        </div>
                        <div class="relative w-full max-w-sm aspect-video rounded-xl overflow-hidden bg-black shadow-inner border border-green-400/50 mt-2">
                            <div class="absolute inset-0 border-[3px] border-green-500/50 m-4 rounded-lg pointer-events-none z-10 border-dashed"></div>
                            <video id="cameraFeed" autoplay playsinline class="w-full h-full object-cover"></video>
                        </div>
                        
                        <div class="flex gap-2 w-full max-w-sm">
                            <button type="button" onclick="capturePhoto()" class="flex-1 bg-slate-900 text-white py-3 rounded-xl text-[11px] font-black uppercase tracking-widest shadow-lg hover:bg-blue-600 transition-all active:scale-95">
                                Snap Photo 📸
                            </button>
                            <button type="button" onclick="stopCamera()" class="px-5 bg-red-50 text-red-600 border border-red-200 rounded-xl text-[11px] font-black uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all active:scale-95">
                                Cancel
                            </button>
                        </div>
                        <canvas id="cameraCanvas" class="hidden"></canvas>
                    </div>

                    <div id="scanLoader" class="hidden mt-4 text-[11px] uppercase tracking-widest text-green-700 font-black flex items-center justify-center gap-2">
                        <span class="w-4 h-4 border-2 border-green-600 border-t-transparent rounded-full animate-spin"></span>
                        Analyzing and Extracting...
                    </div>
                    <div id="scanSuccess" class="hidden mt-4 text-[11px] uppercase tracking-widest text-green-600 font-black bg-green-100 py-2 rounded-lg border border-green-200">
                        ✅ ID Scanned! Verify details below.
                    </div>
                </div>

                <form id="registerForm" action="actions/process_register.php" method="POST" class="space-y-4 md:space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">First Name</label>
                            <input type="text" name="first_name" id="reg_first_name" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Last Name</label>
                            <input type="text" name="last_name" id="reg_last_name" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Student Number</label>
                            <input type="text" name="student_number" id="reg_student_number" required placeholder="e.g. 2023100194" maxlength="10" pattern="[0-9]{10}"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 font-bold focus:border-green-400 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Email Address</label>
                            <input type="email" name="email" id="reg_email" required placeholder="student@tau.edu.ph" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all">
                            <p id="email-feedback" class="text-[10px] font-bold mt-1 hidden"></p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Course / Program</label>
                        <select name="program_id" id="reg_course" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all text-slate-700 cursor-pointer">
                            <option value="">Select your program...</option>
                            <?php foreach($programs as $prog): ?>
                                <option value="<?= $prog['program_id'] ?>"><?= htmlspecialchars($prog['program_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Year Level</label>
                            <select name="year_level" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all text-slate-700 cursor-pointer">
                                <option value="">Select year level...</option>
                                <option value="1st Year">1st Year</option>
                                <option value="2nd Year">2nd Year</option>
                                <option value="3rd Year">3rd Year</option>
                                <option value="4th Year">4th Year</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Gender</label>
                            <select name="gender" required class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all text-slate-700 cursor-pointer">
                                <option value="">Select gender...</option>
                                <option value="Female">Female</option>
                                <option value="Male">Male</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Contact Number</label>
                            <input type="text" name="contact_number" required placeholder="e.g. 09369522832" maxlength="11" oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all font-medium">
                        </div>
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Date of Birth</label>
                            <input type="date" name="date_of_birth" id="reg_dob" required 
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all text-slate-600 font-medium cursor-pointer">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-5">
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Password</label>
                            <div class="relative">
                                <input type="password" name="password" id="reg_password" required class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all">
                                <button type="button" onclick="togglePassword('reg_password', 'eye_icon_reg')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-green-600 focus:outline-none">
                                    <svg id="eye_icon_reg" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>
                             <div id="password-feedback" class="text-xs font-bold mt-3 space-y-1.5 hidden bg-white p-4 rounded-xl border border-slate-100 shadow-sm">
                                <p id="rule-length" class="text-red-500 transition-colors">✗ At least 8 characters</p>
                                <p id="rule-number" class="text-red-500 transition-colors">✗ At least 1 number (0-9)</p>
                                <p id="rule-symbol" class="text-red-500 transition-colors">✗ At least 1 special symbol (!@#$%^&*)</p>
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Confirm Password</label>
                            <div class="relative">
                                <input type="password" name="confirm_password" id="reg_confirm" required class="w-full pl-4 pr-10 py-3 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all">
                                <button type="button" onclick="togglePassword('reg_confirm', 'eye_icon_confirm')" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-green-600 focus:outline-none">
                                    <svg id="eye_icon_confirm" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                </button>
                            </div>
                             <p id="match-feedback" class="text-xs font-bold mt-2 hidden"></p>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-4 px-6 rounded-xl transition-all mt-6 active:scale-[0.98]">
                        Create Account
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Confirm Registration Sub-Modal -->
    <div id="confirmRegisterModal" class="fixed inset-0 z-[100000] bg-slate-950/80 backdrop-blur-md hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl p-6 text-center transform scale-95 transition-transform duration-300" id="confirmModalContent">
            <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 border border-amber-200">⚠️</div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Confirm Registration Details</h3>
            <p class="text-xs text-slate-500 font-medium leading-relaxed mb-6">
                Please make sure all entered records are 100% correct. <br><br>
                <span class="bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-xl font-black uppercase tracking-wide inline-block text-[10px]">
                    The Student Number cannot be edited once submitted.
                </span>
            </p>
            <div class="flex gap-3">
                <button type="button" onclick="closeConfirmModal()" class="flex-1 bg-slate-100 border border-slate-200 text-slate-700 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-200 transition-all">Go Back</button>
                <!-- This now triggers the OTP flow instead of immediate submission -->
                <button type="button" onclick="commitRegistrationForm()" class="flex-1 bg-green-600 text-white py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-green-700 shadow-lg shadow-green-600/20 transition-all">Confirm & Submit</button>
            </div>
        </div>
    </div>

    <!-- ✨ NEW: OTP VERIFICATION MODAL -->
    <div id="otpModal" class="fixed inset-0 z-[100000] bg-slate-950/80 backdrop-blur-md hidden items-center justify-center p-4 opacity-0 transition-opacity duration-300">
        <div class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl p-8 text-center transform scale-95 transition-transform duration-300" id="otpModalContent">
            <div class="w-16 h-16 bg-green-50 text-green-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4 border border-green-200">📧</div>
            <h3 class="text-xl font-black text-slate-900 mb-2">Check Your Email</h3>
            <p class="text-xs text-slate-500 font-medium leading-relaxed mb-6">
                We've sent a 6-digit verification code to <br> <strong id="otpDisplayEmail" class="text-slate-800"></strong>.
            </p>
            
            <div class="mb-6">
                <input type="text" id="otpInput" placeholder="• • • • • •" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full text-center text-3xl tracking-[0.5em] font-black text-slate-900 py-4 border-b-2 border-slate-200 focus:border-green-500 outline-none transition-all placeholder:text-slate-300 placeholder:font-normal">
                <p id="otpErrorMsg" class="text-[10px] font-bold text-red-500 mt-2 hidden">Invalid code. Please try again.</p>
            </div>

            <button type="button" onclick="verifyOTPAndSubmit()" id="btnVerifyOTP" class="w-full bg-green-600 text-white py-4 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-green-700 shadow-lg shadow-green-600/20 transition-all">Verify & Create Account</button>
            
            <button type="button" onclick="closeOtpModal()" class="w-full mt-3 text-[10px] font-bold text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">Cancel</button>
        </div>
    </div>

    <!-- Forgot Password Modal -->
    <div id="forgotModal" class="fixed inset-0 z-[9999] bg-slate-900/60 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="bg-white w-full max-w-[95%] sm:max-w-md rounded-[1.5rem] md:rounded-[2rem] shadow-2xl relative overflow-hidden">
            <button onclick="closeForgotModal()" class="absolute top-4 right-4 md:top-6 md:right-6 w-8 h-8 md:w-10 md:h-10 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center hover:bg-slate-200 hover:text-slate-900 transition-colors z-10">
                <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="p-6 md:p-10">
                <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center text-green-600 mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                </div>
                <h3 class="text-2xl font-black text-slate-900 tracking-tight mb-2">Reset Password</h3>
                <p class="text-slate-500 text-sm font-medium mb-8">Enter the email address associated with your account, and we'll send you a link to reset your password.</p>

                <form id="forgotForm" action="actions/process_forgot_password.php" method="POST" class="space-y-4">
                    <div>
                        <label class="block text-[10px] md:text-xs font-bold text-slate-700 uppercase tracking-widest mb-1 md:mb-2">Email Address</label>
                        <input type="email" name="email" required placeholder="student@tau.edu.ph" class="w-full px-4 py-4 rounded-xl border border-slate-200 bg-slate-50 focus:border-green-400 outline-none transition-all">
                    </div>
                    
                    <button type="submit" class="w-full bg-slate-900 hover:bg-slate-800 text-white font-bold py-4 px-6 rounded-xl transition-all shadow-lg active:scale-[0.98]">
                        Send Reset Link 📧
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
            } else {
                input.type = 'password';
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }

        function resetPasswordUI() {
            if(feedbackBox) {
                feedbackBox.classList.add('hidden');
                updateRuleUI(ruleLength, false, "At least 8 characters");
                updateRuleUI(ruleNumber, false, "At least 1 number (0-9)");
                updateRuleUI(ruleSymbol, false, "At least 1 special symbol");
            }
            if(matchFeedback) matchFeedback.classList.add('hidden');
            
            document.getElementById('reg_password').type = 'password';
            document.getElementById('reg_confirm').type = 'password';

            const emailFeedback = document.getElementById('email-feedback');
            if(emailFeedback) emailFeedback.classList.add('hidden');
        }

        const regModal = document.getElementById('registerModal');
        function openRegisterModal() { 
            regModal.classList.add('modal-active'); 
            document.body.style.overflow = 'hidden'; 
        }
        function closeRegisterModal() { 
            stopCamera(); 
            resetScanState(); 
            regModal.classList.remove('modal-active'); 
            document.body.style.overflow = 'auto'; 
            document.getElementById('registerForm').reset(); 
            resetPasswordUI(); 
        }
        regModal.addEventListener('click', function(e) { if (e.target === regModal) closeRegisterModal(); });

        const forgotModal = document.getElementById('forgotModal');
        function openForgotModal() { 
            forgotModal.classList.add('modal-active'); 
            document.body.style.overflow = 'hidden'; 
        }
        function closeForgotModal() { 
            forgotModal.classList.remove('modal-active'); 
            document.body.style.overflow = 'auto'; 
            document.getElementById('forgotForm').reset(); 
        }
        forgotModal.addEventListener('click', function(e) { if (e.target === forgotModal) closeForgotModal(); });

        // ========================================================
        // ✨ REAL-TIME EMAIL TYPO & FORMAT VALIDATION
        // ========================================================
        const emailInput = document.getElementById('reg_email');
        const emailFeedback = document.getElementById('email-feedback');

        if(emailInput) {
            emailInput.addEventListener('input', function() {
                const email = this.value.trim();
                if (email.length === 0) {
                    emailFeedback.classList.add('hidden');
                    return;
                }

                emailFeedback.classList.remove('hidden');

                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    emailFeedback.className = "text-[10px] font-bold mt-1 text-red-500";
                    emailFeedback.innerText = "✗ Invalid email format";
                    return;
                }

                const domain = email.split('@')[1].toLowerCase();
                const commonTypos = ['gmai.com', 'gmal.com', 'gmail.con', 'yaho.com', 'yahoo.con', 'outlok.com'];
                
                if (commonTypos.includes(domain)) {
                    emailFeedback.className = "text-[10px] font-bold mt-1 text-amber-500";
                    let suggestion = domain.replace(/gmai\.com|gmal\.com|gmail\.con/, 'gmail.com').replace(/yaho\.com|yahoo\.con/, 'yahoo.com').replace('outlok.com', 'outlook.com');
                    emailFeedback.innerText = "⚠️ Did you mean @" + suggestion + "?";
                    return;
                }

                emailFeedback.className = "text-[10px] font-bold mt-1 text-green-600";
                emailFeedback.innerText = "✓ Valid email format";
            });
        }

        // ========================================================
        // REAL-TIME PASSWORD VALIDATION
        // ========================================================
        const passInput = document.getElementById('reg_password');
        const confirmInput = document.getElementById('reg_confirm');
        const feedbackBox = document.getElementById('password-feedback');
        const ruleLength = document.getElementById('rule-length');
        const ruleNumber = document.getElementById('rule-number');
        const ruleSymbol = document.getElementById('rule-symbol');
        const matchFeedback = document.getElementById('match-feedback');

        function updateRuleUI(element, isValid, text) {
            if (isValid) {
                element.classList.remove('text-red-500');
                element.classList.add('text-green-600');
                element.innerText = '✓ ' + text;
            } else {
                element.classList.remove('text-green-600');
                element.classList.add('text-red-500');
                element.innerText = '✗ ' + text;
            }
        }

        function validatePassword() {
            const val = passInput.value;
            if (val.length > 0) feedbackBox.classList.remove('hidden');
            else feedbackBox.classList.add('hidden');

            const isValidLength = val.length >= 8;
            const isValidNumber = /[0-9]/.test(val);
            const isValidSymbol = /[!@#$%^&*()\-_=+{};:,<.>]/.test(val);

            updateRuleUI(ruleLength, isValidLength, "At least 8 characters");
            updateRuleUI(ruleNumber, isValidNumber, "At least 1 number (0-9)");
            updateRuleUI(ruleSymbol, isValidSymbol, "At least 1 special symbol");
            checkMatch();
        }

        function checkMatch() {
            const val1 = passInput.value;
            const val2 = confirmInput.value;

            if (val2.length > 0) {
                matchFeedback.classList.remove('hidden');
                if (val1 === val2) {
                    matchFeedback.classList.remove('text-red-500');
                    matchFeedback.classList.add('text-green-600');
                    matchFeedback.innerText = '✓ Passwords match';
                } else {
                    matchFeedback.classList.remove('text-green-600');
                    matchFeedback.classList.add('text-red-500');
                    matchFeedback.innerText = '✗ Passwords do not match';
                }
            } else {
                matchFeedback.classList.add('hidden');
            }
        }

        if(passInput) passInput.addEventListener('input', validatePassword);
        if(confirmInput) confirmInput.addEventListener('input', checkMatch);

        // ========================================================
        // ✨ UPGRADED: REGISTRATION + OTP INTERCEPT LOGIC
        // ========================================================
        const confirmModal = document.getElementById('confirmRegisterModal');
        const confirmContent = document.getElementById('confirmModalContent');
        const otpModal = document.getElementById('otpModal');
        const otpContent = document.getElementById('otpModalContent');

        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            const studNum = document.getElementById('reg_student_number').value.trim();
            if(studNum.length !== 10) {
                alert("⚠️ REGISTER VALIDATION ERROR:\n\nStudent number must be exactly 10 digits.");
                return;
            }

            const email = document.getElementById('reg_email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if(!emailRegex.test(email)) {
                alert("⚠️ REGISTER VALIDATION ERROR:\n\nPlease enter a valid email address format.");
                return;
            }

            const val = passInput.value;
            if (val.length < 8 || !/[0-9]/.test(val) || !/[!@#$%^&*()\-_=+{};:,<.>]/.test(val) || val !== confirmInput.value) {
                alert("⚠️ SECURITY POLICY CRITERIA BREACHED:\n\nPlease fix password validation errors before attempting configuration storage saves.");
                return;
            }

            openConfirmModal();
        });

        function openConfirmModal() {
            confirmModal.classList.remove('hidden');
            confirmModal.classList.add('flex');
            setTimeout(() => {
                confirmModal.classList.remove('opacity-0');
                confirmContent.classList.remove('scale-95');
            }, 10);
        }

        function closeConfirmModal() {
            confirmModal.classList.add('opacity-0');
            confirmContent.classList.add('scale-95');
            setTimeout(() => {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('flex');
            }, 300);
        }

        // Triggered when user clicks "Confirm & Submit" on the warning modal
        async function commitRegistrationForm() {
            closeConfirmModal();
            
            const email = document.getElementById('reg_email').value.trim();
            const firstName = document.getElementById('reg_first_name').value.trim();
            document.getElementById('otpDisplayEmail').innerText = email;
            
            const formData = new FormData();
            formData.append('email', email);
            formData.append('first_name', firstName);

            try {
                // Trigger the background PHP script to send the email
                const response = await fetch('actions/send_otp.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if(result.success) {
                    // Open the OTP Modal
                    otpModal.classList.remove('hidden'); 
                    otpModal.classList.add('flex');
                    setTimeout(() => { 
                        otpModal.classList.remove('opacity-0'); 
                        otpContent.classList.remove('scale-95'); 
                    }, 10);
                } else {
                    alert("Error sending verification email: " + result.error);
                }
            } catch (error) {
                alert("System error sending email. Please check your connection.");
            }
        }

        function closeOtpModal() {
            otpModal.classList.add('opacity-0'); 
            otpContent.classList.add('scale-95');
            setTimeout(() => { 
                otpModal.classList.add('hidden'); 
                otpModal.classList.remove('flex'); 
            }, 300);
            document.getElementById('otpInput').value = '';
            document.getElementById('otpErrorMsg').classList.add('hidden');
        }

        // Triggered when user clicks "Verify & Create Account"
        async function verifyOTPAndSubmit() {
            const userInput = document.getElementById('otpInput').value.trim();
            const errorMsg = document.getElementById('otpErrorMsg');
            const btn = document.getElementById('btnVerifyOTP');

            if(userInput.length !== 6) {
                errorMsg.innerText = "Please enter the 6-digit code.";
                errorMsg.classList.remove('hidden'); return;
            }

            btn.innerText = "Verifying...";
            btn.disabled = true;

            try {
                const response = await fetch('actions/verify_otp.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'otp=' + encodeURIComponent(userInput)
                });
                
                const result = await response.json();
                
                if(result.success) {
                    // OTP IS CORRECT! Submit the actual form to process_register.php
                    document.getElementById('registerForm').submit();
                } else {
                    errorMsg.innerText = result.error;
                    errorMsg.classList.remove('hidden');
                    btn.innerText = "Verify & Create Account";
                    btn.disabled = false;
                }
            } catch (error) {
                errorMsg.innerText = "System error verifying code.";
                errorMsg.classList.remove('hidden');
                btn.innerText = "Verify & Create Account";
                btn.disabled = false;
            }
        }

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const loginNum = document.getElementById('login_student_number').value.trim();
            if (loginNum.length !== 10) {
                e.preventDefault();
                alert("⚠️ AUTHENTICATION BLOCKED:\n\nYour student number entry must contain exactly 10 numerical digits.");
            }
        });

        // ========================================================
        // ✨ DUAL-CAPTURE (FRONT & BACK) WITH COMPRESSION LOGIC
        // ========================================================
        let videoStream = null;
        const videoElement = document.getElementById('cameraFeed');
        const canvasElement = document.getElementById('cameraCanvas');
        const cameraContainer = document.getElementById('cameraContainer');
        const scanMethodToggle = document.getElementById('scanMethodToggle');
        const scanArea = document.getElementById('scanArea');
        const scanAreaLabel = document.getElementById('scanAreaLabel');

        let captureState = 'front'; // 'front' or 'back'
        let frontBlob = null;
        let backBlob = null;

        function resetScanState() {
            captureState = 'front';
            frontBlob = null;
            backBlob = null;
            document.getElementById('previewContainer').classList.add('hidden');
            document.getElementById('frontPreview').classList.add('hidden');
            document.getElementById('backPreview').classList.add('hidden');
            document.getElementById('frontPreviewText').classList.remove('hidden');
            document.getElementById('backPreviewText').classList.remove('hidden');
            document.getElementById('backPreviewBox').classList.add('border-dashed', 'border-slate-400', 'opacity-70');
            document.getElementById('backPreviewBox').classList.remove('border-green-400', 'opacity-100');
            
            document.getElementById('cameraBtnText').innerText = "Take Photo Live";
            document.getElementById('uploadBtnText').innerText = "Upload Files";
            document.getElementById('scanSuccess').classList.add('hidden');
        }

        document.getElementById('fallbackCameraInput').addEventListener('change', function() {
            if (this.files.length === 0) return;
            
            if (captureState === 'front') {
                frontBlob = this.files[0];
                document.getElementById('previewContainer').classList.remove('hidden');
                
                const fp = document.getElementById('frontPreview');
                fp.src = URL.createObjectURL(frontBlob); 
                fp.classList.remove('hidden');
                document.getElementById('frontPreviewText').classList.add('hidden');
                
                document.getElementById('backPreviewBox').classList.remove('border-dashed', 'border-slate-400', 'opacity-70');
                document.getElementById('backPreviewBox').classList.add('border-green-400', 'opacity-100');
                
                captureState = 'back';
                document.getElementById('cameraBtnText').innerText = "Take BACK ID Photo";
            } else {
                backBlob = this.files[0];
                const bp = document.getElementById('backPreview');
                bp.src = URL.createObjectURL(backBlob); 
                bp.classList.remove('hidden');
                document.getElementById('backPreviewText').classList.add('hidden');
                
                mergeAndProcessOCR();
            }
            this.value = ''; 
        });

        async function startCamera() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                document.getElementById('fallbackCameraInput').click();
                return;
            }

            try {
                videoStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                videoElement.srcObject = videoStream;
                
                cameraContainer.classList.remove('hidden');
                cameraContainer.classList.add('flex');
                scanMethodToggle.classList.add('hidden');
                
                scanArea.classList.remove('border-dashed', 'bg-green-50/50');
                scanArea.classList.add('border-solid', 'bg-slate-900', 'border-slate-800');
                scanAreaLabel.classList.replace('text-green-800', 'text-white');
                
                document.getElementById('previewContainer').classList.remove('hidden');
                updateCameraInstruction();
            } catch (err) {
                alert("Camera access denied. Falling back to native camera/upload app. \n\nError: " + err.message);
                document.getElementById('fallbackCameraInput').click();
            }
        }

        function stopCamera() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
                videoStream = null;
            }
            cameraContainer.classList.add('hidden');
            cameraContainer.classList.remove('flex');
            scanMethodToggle.classList.remove('hidden');
            
            scanArea.classList.add('border-dashed', 'bg-green-50/50');
            scanArea.classList.remove('border-solid', 'bg-slate-900', 'border-slate-800');
            scanAreaLabel.classList.replace('text-white', 'text-green-800');
            
            if (captureState === 'front' && !frontBlob) {
                document.getElementById('previewContainer').classList.add('hidden');
            }
        }

        function updateCameraInstruction() {
            const badge = document.getElementById('camInstructionBadge');
            if(captureState === 'front') {
                badge.innerText = "Position FRONT of ID";
                badge.classList.replace('bg-blue-600', 'bg-green-600');
            } else {
                badge.innerText = "Position BACK of ID";
                badge.classList.replace('bg-green-600', 'bg-blue-600');
            }
        }

        function capturePhoto() {
            const context = canvasElement.getContext('2d');
            canvasElement.width = videoElement.videoWidth;
            canvasElement.height = videoElement.videoHeight;
            context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);

            videoElement.style.opacity = '0.3';
            setTimeout(() => videoElement.style.opacity = '1', 150);

            canvasElement.toBlob(blob => {
                const imgUrl = URL.createObjectURL(blob);
                
                if (captureState === 'front') {
                    frontBlob = blob;
                    
                    const fp = document.getElementById('frontPreview');
                    fp.src = imgUrl; 
                    fp.classList.remove('hidden');
                    document.getElementById('frontPreviewText').classList.add('hidden');
                    
                    document.getElementById('backPreviewBox').classList.remove('border-dashed', 'border-slate-400', 'opacity-70');
                    document.getElementById('backPreviewBox').classList.add('border-green-400', 'opacity-100');
                    
                    captureState = 'back';
                    updateCameraInstruction();
                } else {
                    backBlob = blob;
                    
                    const bp = document.getElementById('backPreview');
                    bp.src = imgUrl; 
                    bp.classList.remove('hidden');
                    document.getElementById('backPreviewText').classList.add('hidden');
                    
                    stopCamera();
                    mergeAndProcessOCR();
                }
            }, 'image/jpeg', 0.95);
        }

        document.getElementById('idUploader').addEventListener('change', function() {
            if (this.files.length >= 2) {
                frontBlob = this.files[0];
                backBlob = this.files[1];
                
                document.getElementById('previewContainer').classList.remove('hidden');
                
                const fp = document.getElementById('frontPreview');
                fp.src = URL.createObjectURL(frontBlob); 
                fp.classList.remove('hidden');
                document.getElementById('frontPreviewText').classList.add('hidden');
                
                document.getElementById('backPreviewBox').classList.remove('border-dashed', 'border-slate-400', 'opacity-70');
                document.getElementById('backPreviewBox').classList.add('border-green-400', 'opacity-100');
                
                const bp = document.getElementById('backPreview');
                bp.src = URL.createObjectURL(backBlob); 
                bp.classList.remove('hidden');
                document.getElementById('backPreviewText').classList.add('hidden');
                
                mergeAndProcessOCR();
            } else if (this.files.length === 1) {
                if (captureState === 'front') {
                    frontBlob = this.files[0];
                    document.getElementById('previewContainer').classList.remove('hidden');
                    
                    const fp = document.getElementById('frontPreview');
                    fp.src = URL.createObjectURL(frontBlob); 
                    fp.classList.remove('hidden');
                    document.getElementById('frontPreviewText').classList.add('hidden');
                    
                    document.getElementById('backPreviewBox').classList.remove('border-dashed', 'border-slate-400', 'opacity-70');
                    document.getElementById('backPreviewBox').classList.add('border-green-400', 'opacity-100');
                    
                    captureState = 'back';
                    document.getElementById('uploadBtnText').innerText = "Upload BACK ID";
                } else {
                    backBlob = this.files[0];
                    const bp = document.getElementById('backPreview');
                    bp.src = URL.createObjectURL(backBlob); 
                    bp.classList.remove('hidden');
                    document.getElementById('backPreviewText').classList.add('hidden');
                    
                    mergeAndProcessOCR();
                }
            }
            this.value = ''; 
        });

        function mergeAndProcessOCR() {
            const loader = document.getElementById('scanLoader');
            const successMsg = document.getElementById('scanSuccess');
            
            loader.classList.remove('hidden');
            successMsg.classList.add('hidden');
            document.getElementById('scanMethodToggle').classList.add('hidden'); 

            const mergeCanvas = document.createElement('canvas');
            const ctx = mergeCanvas.getContext('2d');
            
            const imgFront = new Image();
            const imgBack = new Image();

            imgFront.onload = () => {
                imgBack.onload = () => {
                    const MAX_WIDTH = 1000;

                    let fW = imgFront.width, fH = imgFront.height;
                    if(fW > MAX_WIDTH) { fH = fH * (MAX_WIDTH / fW); fW = MAX_WIDTH; }

                    let bW = imgBack.width, bH = imgBack.height;
                    if(bW > MAX_WIDTH) { bH = bH * (MAX_WIDTH / bW); bW = MAX_WIDTH; }

                    mergeCanvas.width = Math.max(fW, bW);
                    mergeCanvas.height = fH + bH;
                    
                    ctx.fillStyle = "#ffffff";
                    ctx.fillRect(0, 0, mergeCanvas.width, mergeCanvas.height);

                    ctx.drawImage(imgFront, 0, 0, fW, fH);
                    ctx.drawImage(imgBack, 0, fH, bW, bH);
                    
                    mergeCanvas.toBlob(mergedBlob => {
                        const finalFile = new File([mergedBlob], "merged_id.jpg", { type: "image/jpeg" });
                        sendToBackend(finalFile);
                    }, 'image/jpeg', 0.6); 
                };
                imgBack.src = URL.createObjectURL(backBlob);
            };
            imgFront.src = URL.createObjectURL(frontBlob);
        }

        async function sendToBackend(file) {
            const loader = document.getElementById('scanLoader');
            const successMsg = document.getElementById('scanSuccess');
            const toggle = document.getElementById('scanMethodToggle');

            const formData = new FormData();
            formData.append('id_image', file);

            try {
                const response = await fetch('actions/scan_id_ocr.php', {
                    method: 'POST',
                    body: formData
                });
                
                const textResult = await response.text();
                let result;
                try {
                    result = JSON.parse(textResult);
                } catch (e) {
                    console.error("PHP Error Output:", textResult);
                    alert("System Error: The uploaded image might be too large or the server returned an error.");
                    loader.classList.add('hidden');
                    toggle.classList.remove('hidden');
                    resetScanState();
                    return;
                }
                
                loader.classList.add('hidden');
                toggle.classList.remove('hidden'); 
                
                if (result.success) {
                    document.getElementById('reg_first_name').value = result.data.first_name || '';
                    document.getElementById('reg_last_name').value = result.data.last_name || '';
                    document.getElementById('reg_student_number').value = result.data.student_id || '';
                    
                    if (result.data.date_of_birth) {
                        const dobInput = document.getElementById('reg_dob');
                        if (dobInput) {
                            dobInput.value = result.data.date_of_birth;
                            dobInput.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
                            setTimeout(() => dobInput.classList.remove('ring-2', 'ring-green-400', 'bg-green-50'), 2000);
                        }
                    }

                    if (result.data.course) {
                        const programSelect = document.getElementById('reg_course');
                        if (programSelect) {
                            const extractedCourse = result.data.course.toLowerCase();
                            for (let i = 0; i < programSelect.options.length; i++) {
                                const optionText = programSelect.options[i].text.toLowerCase();
                                if (optionText.includes(extractedCourse) || extractedCourse.includes(optionText)) {
                                    programSelect.selectedIndex = i;
                                    programSelect.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
                                    setTimeout(() => programSelect.classList.remove('ring-2', 'ring-green-400', 'bg-green-50'), 2000);
                                    break;
                                }
                            }
                        }
                    }

                    successMsg.classList.remove('hidden');
                    
                    ['reg_first_name', 'reg_last_name', 'reg_student_number'].forEach(id => {
                        const el = document.getElementById(id);
                        if(el && el.value !== '') {
                            el.classList.add('ring-2', 'ring-green-400', 'bg-green-50');
                            setTimeout(() => el.classList.remove('ring-2', 'ring-green-400', 'bg-green-50'), 2000);
                        }
                    });

                    setTimeout(resetScanState, 3000); 
                } else {
                    alert(result.error);
                    resetScanState();
                }
            } catch (error) {
                alert("System error during AI OCR scan. Please check your console.");
                loader.classList.add('hidden');
                toggle.classList.remove('hidden');
                resetScanState();
            }
        }
    </script>
<?php include 'includes/chatbot.php'; ?>
</body>
</html>