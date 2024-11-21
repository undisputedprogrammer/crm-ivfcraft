<?php

use App\Http\Controllers\AdhocRequirementsController;
use App\Models\Message;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\Remarkcontroller;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FollowupController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuditController;
use App\Http\Controllers\WhatsAppApiController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\BreakController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\CreateFollowupController;
use App\Http\Controllers\InternalChatController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\SourceController;
use App\Http\Controllers\UpdateController;
use App\Models\Source;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\CorrectionController;
use App\Http\Controllers\ProcedureController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/logout', [PageController::class, 'destroy'])->name('user-logout');
    Route::get('/overview', [PageController::class, 'overview'])->name('overview');
    Route::get('/performance', [PageController::class, 'performance'])->name('performance');
    Route::get('/leads', [PageController::class, 'leadIndex'])->name('fresh-leads');
    Route::get('/leads/reassign', [TemplateController::class, 'reassign'])->name('leads.reassign');
    Route::post('lead/store', [LeadController::class, 'store'])->name('lead.store');
    Route::post('/lead/refer', [LeadController::class, 'refer'])->name('lead.refer');
    Route::post('/leads/distribute/all', [LeadController::class, 'distribute'])->name('leads.distribute');
    Route::get('/leads/{id}', [LeadController::class, 'show'])->name('leads.show');
    Route::post('/lead/update',[LeadController::class, 'update'])->name('lead.update');
    Route::post('/lead/status-update',[LeadController::class, 'statusUpdate'])->name('lead.status.update');
    Route::post('/remark/store', [Remarkcontroller::class, 'store'])->name('add-remark');
    Route::get('/lead/change/segment', [LeadController::class, 'change'])->name('change-segment');
    Route::get('/lead/change/valid', [LeadController::class, 'changevalid'])->name('change-valid');
    Route::get('/lead/change/genuine', [LeadController::class, 'changeGenuine'])->name('change-genuine');
    Route::get('/lead/answer', [LeadController::class, 'answer'])->name('lead.answer');
    Route::post('lead/close', [LeadController::class, 'close'])->name('lead.close');
    Route::get('/followups', [PageController::class, 'followUps'])->name('followups');
    Route::post('/followup/initiate', [FollowupController::class, 'initiate'])->name('initiate-followup');
    Route::post('/followup/store', [FollowupController::class, 'store'])->name('process-followup');
    Route::get('/search', [PageController::class, 'searchIndex'])->name('search-index');
    Route::post('/search', [SearchController::class, 'index'])->name('get-results');
    Route::post('/followup/new', [FollowupController::class, 'next'])->name('next-followup');
    Route::get('/followup/{id}',[PageController::class, 'showFollowup'])->name('followup.show');
    Route::post('/import/lead', [ImportController::class, 'importLead'])->name('import-leads');
    Route::get('/questions', [PageController::class, 'questionIndex'])->name('manage-questions');
    Route::post('/questions/store', [QuestionController::class, 'store'])->name('add-question');
    Route::post('/questions/update', [QuestionController::class, 'update'])->name('update-question');
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::post('/doctors/{id}', [DoctorController::class, 'update'])->name('doctors.update');
    Route::post('/appointment/store', [AppointmentController::class, 'store'])->name('add-appointment');
    Route::post('/appointment/update',[AppointmentController::class, 'updateAppointment'])->name('appointment.update');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/consulted', [AppointmentController::class, 'consulted'])->name('consulted.mark');
    Route::post('/message/sent', [MessageController::class, 'message'])->name('message.sent');
    Route::post('/treatment-status/update',[LeadController::class, 'setTreatmentStatus'])->name('treatmentStatus.update');
    Route::post('/procedure/store', [ProcedureController::class, 'store'])->name('add-procedure');
    Route::post('/procedure/update',[ProcedureController::class, 'update'])->name('procedure.update');
    Route::post('/procedure/complete', [ProcedureController::class, 'procedureComplete'])->name('procedure.complete');
    Route::get('/sources', [PageController::class, 'sourceIndex'])->name('sources.index');
    // Route::get('/messages',[MessageController::class, 'index'])->name('messages.index');
    // Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    // Route::post('/messages/{id}', [MessageController::class, 'update'])->name('messages.update');
    Route::post('/source/store', [SourceController::class, 'store'])->name('source.store');
    Route::post('/source/update', [SourceController::class, 'update'])->name('source.update');
    Route::get('/source/fetch', [SourceController::class, 'fetch'])->name('sources.fetch');

    Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
    Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
    Route::post('agents/{id}', [AgentController::class, 'update'])->name('agents.update');
    Route::get('/password/reset', [AgentController::class, 'reset'])->name('user-password.reset');
    Route::post('/password/reset', [AgentController::class, 'change'])->name('password.change');

    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaign.index');
    Route::post('/campaigns/all', [CampaignController::class, 'all'])->name('campaign.all');
    Route::post('/campaigns/form-options', [CampaignController::class, 'formOptions'])->name('campaign.form_options');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaign.store');
    Route::post('/campaigns/{id}/toggle', [CampaignController::class, 'toggle'])->name('campaign.toggle');
    Route::post('/campaigns/{id}/toggle-form', [CampaignController::class, 'toggleForm'])->name('campaign.toggle_form');

    Route::get('/templates', [TemplateController::class, 'index'])->name('template.index');
    Route::post('/template', [TemplateController::class, 'store'])->name('template.store');
    Route::post('/leads/reassign', [TemplateController::class, 'assign'])->name('leads.assign');

    Route::get('/leads/{id}', [LeadController::class, 'show'])->name('leads.show');
    // whatsapp api routes
    Route::post('/message/sent', [WhatsAppApiController::class, 'sent'])->name('message.sent');

    Route::get('/messenger', [WhatsAppApiController::class, 'index'])->name('messenger');

    Route::get('/fetch/latest',[WhatsAppApiController::class, 'fetchLatest'])->name('latest.fetch');

    Route::post('/message/bulk/sent',[WhatsAppApiController::class, 'bulkMessage'])->name('message.bulk');

    Route::get('/api/get/remarks', [Remarkcontroller::class, 'getRemarks']);

    Route::get('/api/followup', [Remarkcontroller::class, 'followup']);

    Route::get('/api/get/chats', [WhatsAppApiController::class, 'getChats']);

    Route::post('/api/convert', [FollowupController::class, 'convert']);

    Route::get('/fetch/audits',[AuditController::class, 'fetch'])->name('audits.fetch');

    Route::get('/fetch/journals',[JournalController::class, 'fetch'])->name('journals.fetch');
    Route::get('/fetch/own-journal',[JournalController::class, 'fetch'])->name('journals.fetch_own');

    Route::get('/api/messages/new', [WhatsAppApiController::class, 'unread']);

    Route::get('/api/messages/poll', [WhatsAppApiController::class, 'poll']);

    Route::get('/mark/read',[WhatsAppApiController::class, 'markRead'])->name('mark.read');

    Route::get('/hospotal/centers', [
        HospitalController::class, 'centers'
    ])->name('hospital.centers');

    Route::get('/center/agents',[PageController::class, 'getAgents'])->name('center.agents');

    Route::get('/agents/logged', [AgentController::class, 'getLoggedInAgents'])->name('agents.logged');

    Route::get('/break-in',[BreakController::class, 'breakIn'])->name('break.in');

    Route::post('/break-out',[BreakController::class, 'breakOut'])->name('break.out');

    Route::get('/user/profile',[ProfileController::class, 'edit'])->name('user.profile');

    Route::post('/user/profile/save',[ProfileController::class, 'save'])->name('profile.save');

    Route::post('/journal/store',[JournalController::class, 'store'])->name('journal.store');

    Route::get('/compose/email/{id}',[PageController::class, 'compose'])->name('email.compose');

    Route::post('/email/send',[MailController::class, 'custom'])->name('email.send');

    Route::get('fetch/agents', [AgentController::class, 'fetchAgents'])->name('agents.fetch');
    // autogenerated routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chat-corner', [InternalChatController::class, 'index'])->name('internal_chat.index');
    Route::get('/get-chat-room', [InternalChatController::class, 'getChatRoom'])->name('internal_chat.get_chat_room');
    Route::get('/get-older-chats', [InternalChatController::class, 'olderMessages'])->name('internal_chat.older_messages');
    Route::post('/post-internal-message', [InternalChatController::class, 'postMessage'])->name('internal_chat.post_message');
    // Route::get('/reassignments-list', [AdhocRequirementsController::class, 'reassignedLeads'])->name('leads.reassignments');

    // update routes
    // Route::get('/update/set-call-status', [UpdateController::class, 'setCallStatusForAllLeads']);
    // Route::get('/update/set-sources', [UpdateController::class, 'setDistinctSourcesForHospital']);
    // Route::get('/update/numbers', [UpdateController::class, 'updateAllNumbers']);
    // update routes end


});
Route::get('/reassignments-list', [AdhocRequirementsController::class, 'reassignedLeads'])->name('leads.reassignments');
Route::get('/db-campaign-correction', [CorrectionController::class, 'sanitizeCampaignNames']);
Route::get('/db-status-correction', [CorrectionController::class, 'completedToConsulted']);
Route::get('/db-remove-duplicates', [CorrectionController::class, 'removeDuplicates']);

Route::get('/', [PageController::class, 'home']);

Route::post('/webhook/wa', [WhatsAppApiController::class, 'receive'])->name('webhook.wa');
Route::get('/webhook/wa', [WhatsAppApiController::class, 'verify'])->name('webhook.wa.verify');

require __DIR__ . '/auth.php';
