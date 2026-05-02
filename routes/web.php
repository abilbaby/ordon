<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RecipientInviteRegistrationController;
use App\Http\Controllers\RecipientController;
use App\Http\Controllers\RecipientUpdateRequestController;
use App\Http\Controllers\UpdateRequestController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/contact-us', function () {
    return view('contact');
})->name('contact');

Route::get('/recipient/register', [RecipientInviteRegistrationController::class, 'show'])->name('recipient.invite.register');
Route::post('/recipient/register', [RecipientInviteRegistrationController::class, 'store'])->name('recipient.invite.register.store');

Route::middleware(['auth', 'no-cache'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect(match (auth()->user()->role) {
            'admin' => route('admin.dashboard', absolute: false),
            'donor' => route('donor.dashboard', absolute: false),
            'recipient' => route('recipient.dashboard', absolute: false),
            'hospital' => route('hospital.dashboard', absolute: false),
            default => route('profile.edit', absolute: false),
        });
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

Route::middleware(['auth', 'no-cache', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/auto-matching', [AdminController::class, 'runAutoMatching'])->name('auto-matching');
    Route::get('/reports/export/csv', [AdminController::class, 'exportReportsCsv'])->name('reports.export.csv');
    Route::get('/reports/export/pdf', [AdminController::class, 'exportReportsPdf'])->name('reports.export.pdf');
    Route::get('/donors/export/csv', [AdminController::class, 'exportDonorsCsv'])->name('donors.export.csv');
    Route::get('/recipients/export/csv', [AdminController::class, 'exportRecipientsCsv'])->name('recipients.export.csv');
    Route::get('/matches/export/csv', [AdminController::class, 'exportMatchesCsv'])->name('matches.export.csv');
    Route::get('/hospitals/export/csv', [AdminController::class, 'exportHospitalsCsv'])->name('hospitals.export.csv');
    Route::get('/donors', [AdminController::class, 'donors'])->name('donors');
    Route::get('/recipients', [AdminController::class, 'recipients'])->name('recipients');
    Route::get('/flagged-recipients', [AdminController::class, 'flaggedRecipients'])->name('flagged-recipients');
    Route::get('/matches', [AdminController::class, 'matches'])->name('matches');
    Route::get('/hospitals', [AdminController::class, 'hospitals'])->name('hospitals');
    Route::get('/doctors', [AdminController::class, 'doctors'])->name('doctors');
    Route::get('/organs', [AdminController::class, 'organs'])->name('organs');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');
    Route::get('/donation-logs', [AdminController::class, 'donationLogs'])->name('donation-logs');
    Route::get('/blacklist-registry', [AdminController::class, 'blacklistRegistry'])->name('blacklist-registry');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    Route::post('/donors/{donor}/approve', [AdminController::class, 'approveDonor'])->name('donors.approve');
    Route::post('/donors/{donor}/reject', [AdminController::class, 'rejectDonor'])->name('donors.reject');
    Route::post('/donors/{donor}/flag-fraud', [AdminController::class, 'flagDonorFraud'])->name('donors.flag-fraud');
    Route::post('/donors/{donor}/blacklist', [AdminController::class, 'blacklistDonor'])->name('donors.blacklist');
    Route::post('/hospitals/{hospital}/approve', [AdminController::class, 'approveHospital'])->name('hospitals.approve');
    Route::post('/hospitals/{hospital}/reject', [AdminController::class, 'rejectHospital'])->name('hospitals.reject');
    Route::post('/hospitals/{hospital}/flag-fraud', [AdminController::class, 'flagHospitalFraud'])->name('hospitals.flag-fraud');
    Route::post('/hospitals/{hospital}/blacklist', [AdminController::class, 'blacklistHospital'])->name('hospitals.blacklist');
    Route::post('/matches/{match}/override', [AdminController::class, 'overrideMatch'])->name('matches.override');
    Route::post('/recipients/{recipient}/emergency-priority', [AdminController::class, 'setEmergencyPriority'])->name('recipients.emergency-priority');
    Route::post('/donors/{donor}/verify-identity', [AdminController::class, 'verifyDonorIdentity'])->name('donors.verify-identity');
    Route::post('/recipients/{recipient}/verify-identity', [AdminController::class, 'verifyRecipientIdentity'])->name('recipients.verify-identity');
    Route::post('/recipients/{recipient}/override-approval', [AdminController::class, 'overrideRecipientApproval'])->name('recipients.override-approval');
    Route::post('/hospitals/{hospital}/verify-identity', [AdminController::class, 'verifyHospitalIdentity'])->name('hospitals.verify-identity');
    Route::post('/transplants/{transplant}/certificate-recipient', [AdminController::class, 'setCertificateRecipientName'])->name('transplants.certificate-recipient');
    Route::post('/issue-reports/{issueReport}/resolve', [AdminController::class, 'resolveIssueReport'])->name('issue-reports.resolve');
    Route::post('/issue-reports/{issueReport}/reopen', [AdminController::class, 'reopenIssueReport'])->name('issue-reports.reopen');
});

Route::middleware(['auth', 'no-cache', 'role:donor'])->prefix('donor')->name('donor.')->group(function () {
    Route::get('/dashboard', [DonorController::class, 'dashboard'])->name('dashboard');
    Route::get('/matches', [DonorController::class, 'matches'])->name('matches');
    Route::get('/certificate', [DonorController::class, 'certificate'])->name('certificate');
    Route::get('/certificate/download', [DonorController::class, 'downloadCertificate'])->name('certificate.download');
    Route::post('/profile', [DonorController::class, 'updateProfile'])->name('profile.update');
    Route::post('/availability', [DonorController::class, 'toggleAvailability'])->name('availability.toggle');
    Route::post('/match', [DonorController::class, 'runMatch'])->name('match');
    Route::post('/consent', [DonorController::class, 'updateConsent'])->name('consent.update');
    Route::post('/emergency-requests/{emergencyRequest}/accept', [DonorController::class, 'acceptEmergencyRequest'])->name('emergency.accept');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});

Route::middleware(['auth', 'no-cache', 'role:recipient'])->prefix('recipient')->name('recipient.')->group(function () {
    Route::get('/dashboard', [RecipientController::class, 'dashboard'])->name('dashboard');
    Route::get('/requests', [RecipientController::class, 'requests'])->name('requests');
    Route::get('/profile', [RecipientUpdateRequestController::class, 'profile'])->name('profile');
    Route::patch('/update/direct-fields', [RecipientController::class, 'updateDirectFields'])->name('update.direct-fields');
    Route::post('/update/request/submit', [RecipientUpdateRequestController::class, 'submitUpdateRequest'])->name('update.request.submit');
    Route::get('/matches', [RecipientController::class, 'matches'])->name('matches');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});

Route::middleware(['auth', 'no-cache', 'role:hospital'])->prefix('hospital')->name('hospital.')->group(function () {
    Route::get('/dashboard', [HospitalController::class, 'dashboard'])->name('dashboard');
    Route::get('/invitations', [HospitalController::class, 'invitations'])->name('invitations');
    Route::get('/approvals', [HospitalController::class, 'approvals'])->name('approvals');
    Route::get('/transplants', [HospitalController::class, 'transplants'])->name('transplants');
    Route::get('/planner', [HospitalController::class, 'planner'])->name('planner');
    Route::post('/transplants/{transplant}/slot', [HospitalController::class, 'assignSlot'])->name('transplants.slot');
    Route::get('/doctors/{doctor}/edit', [HospitalController::class, 'editDoctor'])->name('doctors.edit');
    Route::patch('/doctors/{doctor}', [HospitalController::class, 'updateDoctor'])->name('doctors.update');
    Route::delete('/doctors/{doctor}', [HospitalController::class, 'deleteDoctor'])->name('doctors.delete');
    Route::post('/transplants/{transplant}/surgery-workflow', [HospitalController::class, 'updateSurgeryWorkflow'])->name('transplants.surgery-workflow');
    Route::post('/transplants/{transplant}/transport', [HospitalController::class, 'updateTransport'])->name('transplants.transport');
    Route::post('/transplants/{transplant}/post-op-report', [HospitalController::class, 'addPostOperationReport'])->name('transplants.post-op-report');
    Route::post('/transplants/{transplant}/certificate-recipient', [HospitalController::class, 'setCertificateRecipientName'])->name('transplants.certificate-recipient');
    Route::post('/matches/{match}/approve', [HospitalController::class, 'approveMatch'])->name('matches.approve');
    Route::post('/matches/{match}/validate', [HospitalController::class, 'validateMatch'])->name('matches.validate');
    Route::post('/matches/{match}/reject', [HospitalController::class, 'rejectMatch'])->name('matches.reject');
    Route::post('/matches/{match}/complete', [HospitalController::class, 'completeTransplant'])->name('matches.complete');
    Route::post('/recipient/{recipient}/approve', [HospitalController::class, 'approveRecipient'])->name('recipient.approve');
    Route::post('/recipient/{recipient}/reject', [HospitalController::class, 'rejectRecipient'])->name('recipient.reject');
    Route::post('/recipient-change-requests/{changeRequest}/approve', [HospitalController::class, 'approveRecipientChangeRequest'])->name('recipient-change.approve');
    Route::post('/recipient-change-requests/{changeRequest}/reject', [HospitalController::class, 'rejectRecipientChangeRequest'])->name('recipient-change.reject');
    Route::post('/recipient-invites', [HospitalController::class, 'createRecipientInvite'])->name('recipient-invites.create');
    Route::post('/doctors', [HospitalController::class, 'addDoctor'])->name('doctors.add');
    Route::post('/inventory', [HospitalController::class, 'addInventory'])->name('inventory.add');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    
    // Recipient Management
    Route::get('/recipients', [RecipientUpdateRequestController::class, 'recipients'])->name('recipients');
    Route::get('/recipient/{id}/details', [RecipientUpdateRequestController::class, 'recipientDetails'])->name('recipient.details');
    
    // Update Request Management
    Route::get('/update-requests', [UpdateRequestController::class, 'pendingRequests'])->name('update-requests');
    Route::get('/update-history', [UpdateRequestController::class, 'history'])->name('update-history');
    Route::get('/update-request/{id}', [UpdateRequestController::class, 'show'])->name('update-request.show');
    Route::get('/update-history/{id}', [UpdateRequestController::class, 'historyDetails'])->name('update-history.details');
    Route::post('/update-request/{id}/approve-selected', [UpdateRequestController::class, 'approveSelected'])->name('update-request.approve-selected');
    Route::post('/update-request/{id}/reject', [UpdateRequestController::class, 'reject'])->name('update-request.reject');
});

require __DIR__.'/auth.php';
