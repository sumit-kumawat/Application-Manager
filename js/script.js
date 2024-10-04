        // Initialize clipboard.js
        var clipboard = new ClipboardJS('.clipboard-btn');

        clipboard.on('success', function(e) {
            $('#toast').toast('show');
            e.clearSelection();
        });

        $(document).ready(function() {
            // Filter by platform
            $('.filter-btn').on('click', function() {
                var platform = $(this).data('platform').toLowerCase();

                // Toggle active class for the clicked button
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                // Filter the cards
                $('.card').each(function() {
                    var cardPlatform = $(this).find('.platform-logo').attr('alt').toLowerCase();
                    if (cardPlatform === platform) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Clear filters when clicking on search (to keep functionality consistent)
            $('#searchInput').on('keyup', function() {
                $('.filter-btn').removeClass('active');
            });

            // Display today's date in the modal
            var today = new Date();
            var formattedDate = today.toISOString().split('T')[0];
            $('#todayDate').text(formattedDate);

            // Automatically hide toast after 3 seconds
            $('#toast').toast({ delay: 3000 });
        });

        // Handle filter button clicks
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                const platform = this.getAttribute('data-platform');
                window.location.href = `index.php?platform=${platform}`;
            });
        });
        const base_url = '<?php echo $base_url; ?>';

        function fetchApplications(platform = '', query = '') {
            fetch(`index.php?platform=${platform}&query=${query}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('applicationsContainer').innerHTML = html;
                });
        }

        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const platform = this.getAttribute('data-platform');
                const query = document.getElementById('searchInput').value;
                fetchApplications(platform, query);
            });
        });

        document.getElementById('searchInput').addEventListener('input', function() {
            const query = this.value;
            const platform = document.querySelector('.filter-btn.active')?.getAttribute('data-platform') || '';
            fetchApplications(platform, query);
        });

        // Initial load
        fetchApplications();