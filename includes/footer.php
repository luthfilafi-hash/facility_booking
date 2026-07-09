<?php if (!isset($is_dash)): ?>
<footer class="sb-footer">
    <div class="sb-footer-grid">
        <div>
            <h4>UniReserve University</h4>
            <p>Elevating the student athletic experience through seamless, real-time facility and equipment management. Play more, stress less.</p>
        </div>
        <div>
            <h4>Quick Links</h4>
            <ul>
                <li><a href="<?= BASE_URL ?>/index.php">Home</a></li>
                <li><a href="<?= BASE_URL ?>/index.php#facilities">Browse Facilities</a></li>
                <li><a href="<?= BASE_URL ?>/index.php#how-it-works">How It Works</a></li>
                <li><a href="<?= BASE_URL ?>/login.php">Student Portal</a></li>
            </ul>
        </div>
        <div>
            <h4>Contact Us</h4>
            <ul>
                <li><a href="mailto:korporatucs@uitm.edu.my">korporatucs@uitm.edu.my</a></li>
                <li><a href="tel:+60332584000">+603-3258 4000</a></li>
                <li><a href="https://maps.app.goo.gl/is62G3HLcv96q9y49" target="_blank" rel="noopener noreferrer">UITM Puncak Perdana</a></li>
            </ul>
        </div>
    </div>
    <div class="sb-footer-bottom">
        <p>© <?= date('Y') ?> <strong>UniReserve</strong>. All rights reserved.</p>
    </div>
</footer>
<?php else: ?>
        </main> <!-- /sb-dash-content -->
    </div> <!-- /sb-dash-body -->
</div> <!-- /sb-dash -->
<?php endif; ?>

<!-- SweetAlert2 for Premium Modals -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Intercept all inline onsubmit confirm() dialogs
    const forms = document.querySelectorAll('form[onsubmit*="return confirm"]');
    forms.forEach(form => {
        const onsubmitAttr = form.getAttribute('onsubmit');
        const match = onsubmitAttr ? onsubmitAttr.match(/confirm\(['"](.+?)['"]\)/) : null;
        const msg = match ? match[1] : 'Are you sure you want to proceed?';
        
        // Remove the native onsubmit behavior
        form.removeAttribute('onsubmit');
        
        // Attach premium SweetAlert2 modal
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Confirm Action',
                    text: msg,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, proceed',
                    cancelButtonText: 'Cancel',
                    background: 'rgba(15, 23, 42, 0.85)',
                    color: '#f8fafc',
                    backdrop: 'rgba(0, 0, 0, 0.6)',
                    customClass: {
                        popup: 'sb-card', 
                        confirmButton: 'sb-btn sb-btn-primary',
                        cancelButton: 'sb-btn sb-btn-ghost'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        HTMLFormElement.prototype.submit.call(form);
                    }
                });
            } else {
                // Fallback if CDN fails
                if (confirm(msg)) {
                    HTMLFormElement.prototype.submit.call(form);
                }
            }
        });
    });
});
</script>

<!-- Premium Date/Time Picker JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize premium date pickers
    flatpickr("input[type=date]", {
        theme: "dark",
        dateFormat: "Y-m-d",
        altInput: true,
        altFormat: "F j, Y",
    });
    
    // Initialize premium time pickers
    flatpickr("input[type=time]", {
        enableTime: true,
        noCalendar: true,
        dateFormat: "H:i",
        time_24hr: true,
        theme: "dark",
        altInput: true,
        altFormat: "h:i K"
    });
});
</script>

<!-- Premium Select Dropdown JS -->
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize premium dropdowns for all native select elements
    const selects = document.querySelectorAll('select.sb-form-input');
    selects.forEach(select => {
        new Choices(select, {
            searchEnabled: false,
            itemSelectText: '',
            shouldSort: false
        });
    });
});
</script>

</body>
</html>
