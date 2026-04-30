<!-- Success Modal -->
<div id="success-modal" class="modal modal-success">
    <div class="modal-overlay" onclick="window.modalSystem.hide('success-modal')"></div>
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon modal-icon-success">✅</div>
            <h3 class="modal-title">Success</h3>
            <button type="button" class="modal-close" onclick="window.modalSystem.hide('success-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p id="success-message" class="modal-text"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="window.modalSystem.hide('success-modal')">OK</button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="error-modal" class="modal modal-error">
    <div class="modal-overlay" onclick="window.modalSystem.hide('error-modal')"></div>
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon modal-icon-error">❌</div>
            <h3 class="modal-title">Error</h3>
            <button type="button" class="modal-close" onclick="window.modalSystem.hide('error-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p id="error-message" class="modal-text"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="window.modalSystem.hide('error-modal')">OK</button>
        </div>
    </div>
</div>

<!-- Info Modal -->
<div id="info-modal" class="modal modal-info">
    <div class="modal-overlay" onclick="window.modalSystem.hide('info-modal')"></div>
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-icon modal-icon-info">ℹ️</div>
            <h3 class="modal-title">Information</h3>
            <button type="button" class="modal-close" onclick="window.modalSystem.hide('info-modal')">&times;</button>
        </div>
        <div class="modal-body">
            <p id="info-message" class="modal-text"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="window.modalSystem.hide('info-modal')">OK</button>
        </div>
    </div>
</div>
