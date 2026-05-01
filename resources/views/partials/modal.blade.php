<!-- Success Modal -->
<div id="success-modal" class="modal modal-success" aria-hidden="true">
    <div class="modal-overlay" onclick="window.modalSystem.hide('success-modal')"></div>
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="success-modal-title">
        <div class="modal-header">
            <div class="modal-icon modal-icon-success">OK</div>
            <h3 id="success-modal-title" class="modal-title">Success</h3>
            <button type="button" class="modal-close" onclick="window.modalSystem.hide('success-modal')" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
            <p id="success-message" class="modal-text"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary modal-action hidden" data-modal-type="success"></button>
            <button type="button" class="btn btn-secondary modal-cancel" onclick="window.modalSystem.hide('success-modal')">Close</button>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div id="error-modal" class="modal modal-error" aria-hidden="true">
    <div class="modal-overlay" onclick="window.modalSystem.hide('error-modal')"></div>
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="error-modal-title">
        <div class="modal-header">
            <div class="modal-icon modal-icon-error">!</div>
            <h3 id="error-modal-title" class="modal-title">Error</h3>
            <button type="button" class="modal-close" onclick="window.modalSystem.hide('error-modal')" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
            <p id="error-message" class="modal-text"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary modal-action hidden" data-modal-type="error"></button>
            <button type="button" class="btn btn-secondary modal-cancel" onclick="window.modalSystem.hide('error-modal')">Close</button>
        </div>
    </div>
</div>

<!-- Info Modal -->
<div id="info-modal" class="modal modal-info" aria-hidden="true">
    <div class="modal-overlay" onclick="window.modalSystem.hide('info-modal')"></div>
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="info-modal-title">
        <div class="modal-header">
            <div class="modal-icon modal-icon-info">i</div>
            <h3 id="info-modal-title" class="modal-title">Information</h3>
            <button type="button" class="modal-close" onclick="window.modalSystem.hide('info-modal')" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
            <p id="info-message" class="modal-text"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary modal-action hidden" data-modal-type="info"></button>
            <button type="button" class="btn btn-secondary modal-cancel" onclick="window.modalSystem.hide('info-modal')">Close</button>
        </div>
    </div>
</div>
