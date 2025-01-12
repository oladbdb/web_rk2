document.addEventListener('DOMContentLoaded', function() {
    const viewControls = document.querySelector('.view-controls');
    const allSections = document.querySelectorAll('section');
    
    if (viewControls && allSections.length > 0) {
        viewControls.addEventListener('click', function(e) {
            if (e.target.classList.contains('view-button')) {
                const view = e.target.dataset.view;
                
                allSections.forEach(section => {
                    section.classList.remove('grid-view', 'list-view', 'compact-view');
                    section.classList.add(view);
                });
                
                viewControls.querySelectorAll('.view-button').forEach(btn => {
                    btn.classList.remove('active');
                });
                e.target.classList.add('active');
            }
        });
    }
}); 